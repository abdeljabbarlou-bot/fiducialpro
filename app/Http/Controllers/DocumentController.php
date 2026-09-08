<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\Document;
use App\Models\Dossier;
use App\Services\DocumentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['client', 'dossier', 'uploader']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $documents = $query->orderByDesc('id')->paginate(12)->withQueryString();
        $clients = Client::orderBy('company_name')->get();
        $dossiers = Dossier::orderBy('reference')->get();

        return view('documents.index', compact('documents', 'clients', 'dossiers'));
    }

    public function create(Request $request)
    {
        $clients = Client::orderBy('company_name')->get();
        $dossiers = Dossier::orderBy('reference')->get();
        $selectedClientId = $request->query('client_id');
        $selectedDossierId = $request->query('dossier_id');

        return view('documents.create', compact('clients', 'dossiers', 'selectedClientId', 'selectedDossierId'));
    }

    public function store(Request $request, DocumentService $documentService)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:contrat,facture,releve_bancaire,declaration_fiscale,bilan,pv,cin,rc,statuts,attestation,document_comptable,autre',
            'client_id' => 'nullable|exists:clients,id',
            'dossier_id' => 'nullable|exists:dossiers,id',
            'file' => 'required|file|max:10240|mimes:pdf,docx,xlsx,jpg,jpeg,png',
            'notes' => 'nullable|string',
        ]);

        try {
            $document = $documentService->uploadDocument($request->file('file'), $validated, auth()->id());
            return redirect()->route('documents.index')->with('success', "Le document '{$document->title}' a été archivé avec succès.");
        } catch (Exception $e) {
            return back()->withErrors(['file' => $e->getMessage()])->withInput();
        }
    }

    public function download(Document $document)
    {
        $disk = Storage::disk('local')->exists($document->file_path) ? 'local' : (Storage::disk('public')->exists($document->file_path) ? 'public' : null);

        if ($disk) {
            ActivityLog::log(
                action: 'telechargement',
                module: 'documents',
                description: "Téléchargement du document '{$document->title}'",
                targetId: $document->id,
                targetLabel: $document->title
            );
            return Storage::disk($disk)->download($document->file_path, $document->file_name);
        }

        return back()->with('error', "Le fichier physique est introuvable sur le serveur.");
    }

    public function destroy(Document $document)
    {
        $this->authorize('delete', $document);

        $title = $document->title;
        $id = $document->id;

        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        } elseif (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        ActivityLog::log(
            action: 'suppression',
            module: 'documents',
            description: "Suppression du document '{$title}'",
            targetId: $id,
            targetLabel: $title
        );

        return redirect()->route('documents.index')->with('success', "Le document '{$title}' a été supprimé.");
    }
}
