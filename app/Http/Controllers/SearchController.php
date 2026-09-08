<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Declaration;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Invoice;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));

        if (empty($q)) {
            return redirect()->route('dashboard');
        }

        $clients = Client::search($q)->take(5)->get();
        $dossiers = Dossier::search($q)->with('client')->take(5)->get();
        $invoices = Invoice::search($q)->with('client')->take(5)->get();
        $declarations = Declaration::search($q)->with(['client', 'type'])->take(5)->get();
        $documents = Document::search($q)->with('client')->take(5)->get();

        $totalResults = $clients->count() + $dossiers->count() + $invoices->count() + $declarations->count() + $documents->count();

        $query = $q;
        return view('search.results', compact('q', 'query', 'clients', 'dossiers', 'invoices', 'declarations', 'documents', 'totalResults'));
    }
}
