<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'client_id',
        'dossier_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'extension',
        'uploaded_by',
        'notes',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class)->withTrashed();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('file_name', 'like', "%{$term}%")
              ->orWhereHas('client', function ($cq) use ($term) {
                  $cq->where('company_name', 'like', "%{$term}%");
              });
        });
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' Mo';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' Ko';
        }
        return $bytes . ' octets';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'contrat' => 'Contrat',
            'facture' => 'Facture fournisseur / justificatif',
            'releve_bancaire' => 'Relevé bancaire',
            'declaration_fiscale' => 'Déclaration fiscale',
            'bilan' => 'Bilan comptable',
            'pv' => 'Procès-verbal (PV)',
            'cin' => "Copie CIN d'un gérant",
            'rc' => 'Modèle J / RC',
            'statuts' => 'Statuts de société',
            'attestation' => 'Attestation fiscale / administrative',
            'document_comptable' => 'Pièce comptable',
            default => ucfirst($this->category),
        };
    }
}
