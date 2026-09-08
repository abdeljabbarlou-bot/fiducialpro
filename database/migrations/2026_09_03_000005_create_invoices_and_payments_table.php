<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Ex: FAC-2026-0001
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('description')->nullable();
            $table->decimal('subtotal_ht', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_ttc', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            $table->enum('status', [
                'brouillon',
                'emise',
                'partiellement_payee',
                'payee',
                'en_retard',
                'annulee'
            ])->default('brouillon');
            $table->string('payment_conditions')->default('Paiement à réception ou sous 30 jours');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(20.00); // 20% TVA au Maroc
            $table->decimal('total_ht', 12, 2)->default(0);
            $table->decimal('total_ttc', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', [
                'especes',
                'virement',
                'cheque',
                'carte',
                'autre'
            ])->default('virement');
            $table->string('reference')->nullable(); // Numéro chèque / bordereau
            $table->string('bank')->nullable(); // Banque émettrice
            $table->text('comments')->nullable();
            $table->string('receipt_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
