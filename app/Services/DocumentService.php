<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Document;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class DocumentService
{
    protected array $allowedExtensions = ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png'];
    protected int $maxSize = 10485760; // 10 Mo en octets

    // Contenu réel (magic bytes détectés par fileinfo) toléré pour chaque extension déclarée,
    // afin d'empêcher un fichier renommé (ex: script.php -> facture.pdf) de passer la validation.
    protected array $allowedMimesByExtension = [
        'pdf' => ['application/pdf'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
        ],
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
        ],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
    ];

    /**
     * Uploader et enregistrer un document dans la GED
     */
    public function uploadDocument(UploadedFile $file, array $data, int $userId): Document
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $this->allowedExtensions)) {
            throw new Exception("Format de fichier non autorisé (.{$extension}). Formats acceptés : PDF, DOCX, XLSX, JPG, PNG.");
        }

        // Vérification du contenu réel du fichier (et non de la seule extension déclarée)
        $realMimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file->getRealPath());
        $expectedMimes = $this->allowedMimesByExtension[$extension] ?? [];

        if (!in_array($realMimeType, $expectedMimes, true)) {
            throw new Exception("Le contenu du fichier ne correspond pas à son extension déclarée (.{$extension}). Fichier rejeté pour des raisons de sécurité.");
        }

        if ($file->getSize() > $this->maxSize) {
            throw new Exception("Le fichier dépasse la taille maximale autorisée de 10 Mo.");
        }

        // RG12 : Doit être associé à un client ou à un dossier
        if (empty($data['client_id']) && empty($data['dossier_id'])) {
            throw new Exception("Un document doit obligatoirement être rattaché à un client ou à un dossier.");
        }

        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;
        $path = $file->storeAs('documents', $fileName, 'local');

        $document = Document::create([
            'title' => $data['title'] ?? $file->getClientOriginalName(),
            'category' => $data['category'] ?? 'document_comptable',
            'client_id' => $data['client_id'] ?? null,
            'dossier_id' => $data['dossier_id'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType() ?? 'application/octet-stream',
            'extension' => $extension,
            'uploaded_by' => $userId,
            'notes' => $data['notes'] ?? null,
        ]);

        ActivityLog::log(
            action: 'upload',
            module: 'documents',
            description: "Ajout du document '{$document->title}' ({$document->formatted_size})",
            targetId: $document->id,
            targetLabel: $document->title
        );

        return $document;
    }
}
