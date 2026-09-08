<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TrashController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- Authentification ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil utilisateur
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Recherche globale
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/archive', [ClientController::class, 'archive'])->name('clients.archive');
    Route::post('/clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');

    // Dossiers
    Route::resource('dossiers', DossierController::class);
    Route::post('/dossiers/{dossier}/assign-employee', [DossierController::class, 'assignEmployee'])->name('dossiers.assign-employee');
    Route::delete('/dossiers/{dossier}/remove-employee/{employee}', [DossierController::class, 'removeEmployee'])->name('dossiers.remove-employee');

    // Déclarations fiscales
    Route::resource('declarations', DeclarationController::class);
    Route::post('/declarations/{declaration}/mark-filed', [DeclarationController::class, 'markAsFiled'])->name('declarations.mark-filed');

    // Factures & Facturation
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

    // Opérations financières sensibles (Admin, Gérant, Comptable uniquement)
    Route::middleware('role:admin,gerant,comptable')->group(function () {
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    });

    // Consultation des factures
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Paiements (Consultation)
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Documents (GED)
    Route::resource('documents', DocumentController::class)->except(['edit', 'update', 'show']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Échéances & Calendrier
    Route::get('/deadlines', [DeadlineController::class, 'index'])->name('deadlines.index');
    Route::post('/deadlines', [DeadlineController::class, 'store'])->name('deadlines.store');
    Route::post('/deadlines/{deadline}/toggle', [DeadlineController::class, 'toggleStatus'])->name('deadlines.toggle');
    Route::delete('/deadlines/{deadline}', [DeadlineController::class, 'destroy'])->name('deadlines.destroy');

    // Employés (Collaborateurs)
    Route::resource('employees', EmployeeController::class);

    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::match(['get', 'post'], '/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Journal d'Audit & Corbeille (Administrateurs & Gérants)
    Route::middleware('role:admin,gerant')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Corbeille : fiches supprimées logiquement, restaurables (RG02)
        Route::get('/corbeille', [TrashController::class, 'index'])->name('trash.index');
        Route::post('/corbeille/clients/{client}/restaurer', [TrashController::class, 'restoreClient'])
            ->withTrashed()->name('trash.restore-client');
        Route::post('/corbeille/dossiers/{dossier}/restaurer', [TrashController::class, 'restoreDossier'])
            ->withTrashed()->name('trash.restore-dossier');
        Route::post('/corbeille/collaborateurs/{employee}/restaurer', [TrashController::class, 'restoreEmployee'])
            ->withTrashed()->name('trash.restore-employee');
    });

    // Gestion des utilisateurs (Administrateurs uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});
