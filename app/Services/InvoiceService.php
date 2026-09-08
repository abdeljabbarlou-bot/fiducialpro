<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Générer une référence de facture unique (ex: FAC-2026-0005)
     */
    public function generateReference(): string
    {
        $year = Carbon::now()->year;
        $latest = Invoice::withTrashed()
            ->where('reference', 'like', "FAC-{$year}-%")
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            $number = 1;
        } else {
            $parts = explode('-', $latest->reference);
            $lastNumber = isset($parts[2]) ? (int) $parts[2] : 0;
            $number = $lastNumber + 1;
        }

        return sprintf('FAC-%d-%04d', $year, $number);
    }

    /**
     * Créer une facture avec ses lignes.
     *
     * La numérotation chronologique (RG06) doit rester continue et sans doublon :
     * si deux factures sont émises simultanément, la contrainte d'unicité en base
     * rejette la seconde — on régénère alors la référence et on retente, au lieu
     * de laisser remonter une erreur serveur.
     */
    public function createInvoice(array $data, array $items, int $userId): Invoice
    {
        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return $this->persistInvoice($data, $items, $userId);
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt === $maxAttempts) {
                    throw $e;
                }
                // Référence déjà prise par une facture émise en parallèle : on retente
            }
        }

        throw new Exception("Impossible de générer une référence de facture unique après {$maxAttempts} tentatives.");
    }

    protected function persistInvoice(array $data, array $items, int $userId): Invoice
    {
        return DB::transaction(function () use ($data, $items, $userId) {
            $data['reference'] = $this->generateReference();
            $data['created_by'] = $userId;
            $data['status'] = $data['status'] ?? 'emise';

            $invoice = Invoice::create($data);

            foreach ($items as $item) {
                if (!empty($item['description']) && !empty($item['unit_price'])) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $item['tax_rate'] ?? 20.00,
                    ]);
                }
            }

            $invoice->recalculateTotals();

            ActivityLog::log(
                action: 'creation',
                module: 'invoices',
                description: "Création de la facture {$invoice->reference} pour le client #{$invoice->client_id} (Total: {$invoice->total_ttc} DH TTC)",
                targetId: $invoice->id,
                targetLabel: $invoice->reference
            );

            return $invoice;
        });
    }

    /**
     * Mettre à jour une facture
     */
    public function updateInvoice(Invoice $invoice, array $data, array $items): Invoice
    {
        return DB::transaction(function () use ($invoice, $data, $items) {
            $invoice->update($data);

            // Remplacer les lignes d'articles
            $invoice->items()->delete();
            foreach ($items as $item) {
                if (!empty($item['description']) && !empty($item['unit_price'])) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $item['tax_rate'] ?? 20.00,
                    ]);
                }
            }

            $invoice->recalculateTotals();

            ActivityLog::log(
                action: 'modification',
                module: 'invoices',
                description: "Modification de la facture {$invoice->reference}",
                targetId: $invoice->id,
                targetLabel: $invoice->reference
            );

            return $invoice;
        });
    }

    /**
     * Générer le document PDF pour la facture
     */
    public function generatePdf(Invoice $invoice)
    {
        $invoice->load(['client.contacts', 'items', 'payments']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }
}
