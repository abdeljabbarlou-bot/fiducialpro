<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Declaration;
use App\Models\DeclarationType;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Services\DeclarationService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiduciaireBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test RG01 & Client creation
     */
    public function test_client_creation_and_ice_format(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();

        $response = $this->actingAs($admin)->post('/clients', [
            'type' => 'entreprise',
            'company_name' => 'Test Entreprise SARL',
            'ice' => '002999888000077',
            'if_number' => '12345678',
            'rc_number' => '99887',
            'legal_form' => 'SARL',
            'city' => 'Casablanca',
            'status' => 'actif',
            'contact_first_name' => 'Karim',
            'contact_last_name' => 'El Fassi',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'company_name' => 'Test Entreprise SARL',
            'ice' => '002999888000077',
        ]);
    }

    /**
     * Test RG03 & RG04: Dossier employee assignment (N:N relationship)
     */
    public function test_dossier_employee_assignment(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $client = Client::first();
        $employee = Employee::first();

        $dossier = Dossier::create([
            'reference' => 'DOS-2026-9999',
            'client_id' => $client->id,
            'type' => 'comptabilite',
            'start_date' => now(),
            'status' => 'en_cours',
            'priority' => 'haute',
        ]);

        $response = $this->actingAs($admin)->post("/dossiers/{$dossier->id}/assign-employee", [
            'employee_id' => $employee->id,
            'role_in_dossier' => 'Chef de mission',
            'assigned_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertTrue($dossier->employees()->where('employee_id', $employee->id)->exists());
    }

    /**
     * Test RG06: Invoice items auto-calculate subtotal_ht, tax_amount and total_ttc
     */
    public function test_invoice_totals_calculation(): void
    {
        $client = Client::first();

        $invoice = Invoice::create([
            'reference' => 'FAC-2026-9991',
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'emise',
        ]);

        // Add line item 1: 2 units * 1000 DH = 2000 DH HT, 20% TVA = 400 DH -> 2400 DH TTC
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Tenue comptable',
            'quantity' => 2,
            'unit_price' => 1000,
            'tax_rate' => 20,
        ]);

        // Add line item 2: 1 unit * 500 DH = 500 DH HT, 20% TVA = 100 DH -> 600 DH TTC
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Déclaration fiscale',
            'quantity' => 1,
            'unit_price' => 500,
            'tax_rate' => 20,
        ]);

        $invoice->recalculateTotals();
        $invoice->refresh();

        $this->assertEquals(2500.00, $invoice->subtotal_ht);
        $this->assertEquals(500.00, $invoice->tax_amount);
        $this->assertEquals(3000.00, $invoice->total_ttc);
        $this->assertEquals(3000.00, $invoice->remaining_amount);
        $this->assertEquals(0.00, $invoice->paid_amount);
    }

    /**
     * Test RG08: Payment validation: amount cannot exceed invoice remaining balance
     */
    public function test_payment_cannot_exceed_remaining_amount(): void
    {
        $client = Client::first();

        $invoice = Invoice::create([
            'reference' => 'FAC-2026-9992',
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'emise',
            'subtotal_ht' => 1000,
            'tax_amount' => 200,
            'total_ttc' => 1200,
            'paid_amount' => 0,
            'remaining_amount' => 1200,
        ]);

        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $paymentService = new PaymentService();

        // Attempting to pay 1500 DH when remaining is 1200 DH must throw an exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('dépasse le solde restant à payer');

        $paymentService->recordPayment($invoice, [
            'amount' => 1500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'virement',
        ], $admin->id);
    }

    /**
     * Test RG07 & RG08: Successful payment updates invoice paid_amount, remaining_amount, and status
     */
    public function test_payment_updates_invoice_status_to_paid(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $client = Client::first();

        $invoice = Invoice::create([
            'reference' => 'FAC-2026-9993',
            'client_id' => $client->id,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'status' => 'emise',
            'subtotal_ht' => 1000,
            'tax_amount' => 200,
            'total_ttc' => 1200,
            'paid_amount' => 0,
            'remaining_amount' => 1200,
        ]);

        $paymentService = new PaymentService();

        // Step 1: Partial payment of 500 DH
        $paymentService->recordPayment($invoice, [
            'amount' => 500.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'virement',
        ], $admin->id);

        $invoice->refresh();
        $this->assertEquals(500.00, $invoice->paid_amount);
        $this->assertEquals(700.00, $invoice->remaining_amount);
        $this->assertEquals('partiellement_payee', $invoice->status);

        // Step 2: Pay the remainder of 700 DH
        $paymentService->recordPayment($invoice, [
            'amount' => 700.00,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cheque',
        ], $admin->id);

        $invoice->refresh();
        $this->assertEquals(1200.00, $invoice->paid_amount);
        $this->assertEquals(0.00, $invoice->remaining_amount);
        $this->assertEquals('payee', $invoice->status);
    }

    /**
     * Test RG11: Automatic overdue declaration detection
     */
    public function test_declaration_overdue_sync(): void
    {
        $client = Client::first();
        $type = DeclarationType::first();

        // Create an overdue declaration (due 5 days ago)
        $declaration = Declaration::create([
            'client_id' => $client->id,
            'declaration_type_id' => $type->id,
            'period' => 'Retard Test 2026',
            'due_date' => now()->subDays(5),
            'amount' => 5000,
            'status' => 'a_preparer',
        ]);

        $declarationService = new DeclarationService();
        $declarationService->syncOverdueDeclarations();

        $declaration->refresh();
        $this->assertEquals('en_retard', $declaration->status);
    }

    /**
     * Test RBAC: Secrétaire cannot access user administration
     */
    public function test_rbac_secretaire_cannot_access_user_management(): void
    {
        $secretaire = User::where('email', 'secretaire@cabinet.ma')->first();

        $response = $this->actingAs($secretaire)->get('/users');
        $response->assertStatus(403);
    }

    /**
     * Test RBAC: Admin can access user administration
     */
    public function test_rbac_admin_can_access_user_management(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();

        $response = $this->actingAs($admin)->get('/users');
        $response->assertStatus(200);
    }

    /**
     * Test RG01: ICE must be strictly 15 numeric digits
     */
    public function test_client_ice_validation_rules(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();

        // 1. Invalid: 14 digits
        $res1 = $this->actingAs($admin)->post('/clients', [
            'type' => 'entreprise',
            'company_name' => 'Invalid ICE 14',
            'ice' => '12345678901234',
            'status' => 'actif',
        ]);
        $res1->assertSessionHasErrors('ice');

        // 2. Invalid: contains letters
        $res2 = $this->actingAs($admin)->post('/clients', [
            'type' => 'entreprise',
            'company_name' => 'Invalid ICE Letters',
            'ice' => '12345678901234A',
            'status' => 'actif',
        ]);
        $res2->assertSessionHasErrors('ice');

        // 3. Valid: exactly 15 digits
        $res3 = $this->actingAs($admin)->post('/clients', [
            'type' => 'entreprise',
            'company_name' => 'Valid ICE 15',
            'ice' => '123456789012345',
            'status' => 'actif',
        ]);
        $res3->assertSessionHasNoErrors();
        $this->assertDatabaseHas('clients', ['company_name' => 'Valid ICE 15', 'ice' => '123456789012345']);
    }

    /**
     * Test User create and edit views render with status 200
     */
    public function test_user_create_and_edit_views_render(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();

        $resCreate = $this->actingAs($admin)->get('/users/create');
        $resCreate->assertStatus(200);

        $resEdit = $this->actingAs($admin)->get("/users/{$admin->id}/edit");
        $resEdit->assertStatus(200);
    }

    /**
     * Test User deletion and self-deletion prevention
     */
    public function test_user_deletion_and_self_prevention(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $roleComptable = Role::where('slug', 'comptable')->first();

        // Admin attempting to delete self must be blocked
        $resSelf = $this->actingAs($admin)->delete("/users/{$admin->id}");
        $resSelf->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // Admin deleting another user must succeed
        $targetUser = User::create([
            'name' => 'Delete Target',
            'email' => 'to_delete@cabinet.ma',
            'password' => bcrypt('password'),
            'role_id' => $roleComptable->id,
            'status' => 'actif',
        ]);

        $resDelete = $this->actingAs($admin)->delete("/users/{$targetUser->id}");
        $resDelete->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    /**
     * Test RBAC: Secretaire cannot perform financial writes (POST /payments, DELETE /invoices)
     */
    public function test_rbac_secretaire_denied_financial_operations(): void
    {
        $secretaire = User::where('email', 'secretaire@cabinet.ma')->first();
        $invoice = Invoice::first();

        // Secretaire cannot post payments -> 403
        $resPayment = $this->actingAs($secretaire)->post('/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 100,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'especes',
        ]);
        $resPayment->assertStatus(403);

        // Secretaire cannot delete invoice -> 403
        $resDelete = $this->actingAs($secretaire)->delete("/invoices/{$invoice->id}");
        $resDelete->assertStatus(403);
    }

    /**
     * Test Notifications and Reports pages return HTTP 200
     */
    public function test_notifications_and_reports_pages(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();

        $resNotif = $this->actingAs($admin)->get('/notifications');
        $resNotif->assertStatus(200);

        $resReports = $this->actingAs($admin)->get('/reports');
        $resReports->assertStatus(200);

        $resExcel = $this->actingAs($admin)->post('/reports/generate', [
            'report_type' => 'financial',
            'format' => 'excel',
        ]);
        $resExcel->assertStatus(200);
        $resExcel->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test RBAC (matrice docs/LIVRABLE_7 §4) : le Comptable peut lire/modifier un client
     * mais ne peut ni le créer ni le supprimer (droit réservé à Admin/Gérant).
     */
    public function test_rbac_comptable_cannot_create_or_delete_client(): void
    {
        $comptable = User::where('email', 'comptable1@cabinet.ma')->first();
        $client = Client::first();

        $resCreate = $this->actingAs($comptable)->get('/clients/create');
        $resCreate->assertStatus(403);

        $resStore = $this->actingAs($comptable)->post('/clients', [
            'type' => 'entreprise',
            'company_name' => 'Interdit SARL',
            'status' => 'actif',
        ]);
        $resStore->assertStatus(403);

        $resDelete = $this->actingAs($comptable)->delete("/clients/{$client->id}");
        $resDelete->assertStatus(403);

        // En revanche la modification lui reste autorisée
        $resEdit = $this->actingAs($comptable)->get("/clients/{$client->id}/edit");
        $resEdit->assertStatus(200);
    }

    /**
     * Test RBAC (matrice docs/LIVRABLE_7 §4) : la Secrétaire peut créer un client
     * (accueil clients) mais ne peut ni le modifier ni le supprimer.
     */
    public function test_rbac_secretaire_cannot_update_or_delete_client(): void
    {
        $secretaire = User::where('email', 'secretaire@cabinet.ma')->first();
        $client = Client::first();

        $resCreate = $this->actingAs($secretaire)->get('/clients/create');
        $resCreate->assertStatus(200);

        $resEdit = $this->actingAs($secretaire)->get("/clients/{$client->id}/edit");
        $resEdit->assertStatus(403);

        $resUpdate = $this->actingAs($secretaire)->put("/clients/{$client->id}", [
            'type' => 'entreprise',
            'company_name' => 'Modification Interdite',
            'status' => 'actif',
        ]);
        $resUpdate->assertStatus(403);

        $resDelete = $this->actingAs($secretaire)->delete("/clients/{$client->id}");
        $resDelete->assertStatus(403);
    }

    /**
     * Test RBAC (matrice docs/LIVRABLE_7 §4) : la Secrétaire n'a qu'un droit de consultation
     * sur les dossiers de mission (aucune création/modification/suppression).
     */
    public function test_rbac_secretaire_dossiers_consultation_only(): void
    {
        $secretaire = User::where('email', 'secretaire@cabinet.ma')->first();
        $dossier = Dossier::first();

        $resIndex = $this->actingAs($secretaire)->get('/dossiers');
        $resIndex->assertStatus(200);

        $resShow = $this->actingAs($secretaire)->get("/dossiers/{$dossier->id}");
        $resShow->assertStatus(200);

        $resCreate = $this->actingAs($secretaire)->get('/dossiers/create');
        $resCreate->assertStatus(403);

        $resDelete = $this->actingAs($secretaire)->delete("/dossiers/{$dossier->id}");
        $resDelete->assertStatus(403);
    }

    /**
     * Test RG06 : seuls les taux de TVA légaux marocains (20, 14, 10, 7, 0 %) sont acceptés.
     */
    public function test_invoice_rejects_illegal_tva_rate(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $client = Client::first();

        $payload = [
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'status' => 'emise',
            'items' => [
                ['description' => 'Prestation test', 'quantity' => 1, 'unit_price' => 1000, 'tax_rate' => 250],
            ],
        ];

        $response = $this->actingAs($admin)->post('/invoices', $payload);
        $response->assertSessionHasErrors('items.0.tax_rate');

        // Un taux légal passe sans erreur
        $payload['items'][0]['tax_rate'] = 14;
        $ok = $this->actingAs($admin)->post('/invoices', $payload);
        $ok->assertSessionHasNoErrors();
    }

    /**
     * Test RG07 : un encaissement ne peut être ni postdaté, ni antérieur à l'émission de la facture.
     */
    public function test_payment_date_must_be_coherent(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $invoice = Invoice::where('remaining_amount', '>', 0)->first();

        // Paiement dans le futur -> refusé
        $future = $this->actingAs($admin)->post('/payments', [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->addMonth()->toDateString(),
            'amount' => 100,
            'payment_method' => 'virement',
        ]);
        $future->assertSessionHasErrors('payment_date');

        // Paiement antérieur à la facture -> refusé
        $tooEarly = $this->actingAs($admin)->post('/payments', [
            'invoice_id' => $invoice->id,
            'payment_date' => $invoice->invoice_date->copy()->subDay()->toDateString(),
            'amount' => 100,
            'payment_method' => 'virement',
        ]);
        $tooEarly->assertSessionHasErrors('payment_date');

        // Date cohérente -> accepté
        $valid = $this->actingAs($admin)->post('/payments', [
            'invoice_id' => $invoice->id,
            'payment_date' => now()->toDateString(),
            'amount' => 100,
            'payment_method' => 'virement',
        ]);
        $valid->assertSessionHasNoErrors();
    }

    /**
     * Test RG15 : blocage temporaire après 5 tentatives de connexion échouées (anti force brute).
     */
    public function test_login_is_rate_limited_after_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'admin@cabinet.ma',
                'password' => 'mauvais-mot-de-passe',
            ]);
        }

        // La 6ème tentative est bloquée, même avec le bon mot de passe
        $blocked = $this->post('/login', [
            'email' => 'admin@cabinet.ma',
            'password' => 'password',
        ]);

        $blocked->assertSessionHasErrors('email');
        $this->assertGuest();

        // Les tentatives échouées sont tracées dans le journal d'audit (RG14)
        $this->assertDatabaseHas('activity_logs', ['action' => 'connexion_echouee']);
    }

    /**
     * Test RBAC (matrice §4) : la Secrétaire ne fait que consulter les déclarations fiscales ;
     * la saisie et le télé-dépôt SIMPL engagent le cabinet et restent aux comptables.
     */
    public function test_rbac_secretaire_declarations_consultation_only(): void
    {
        $secretaire = User::where('email', 'secretaire@cabinet.ma')->first();
        $declaration = Declaration::first();

        $this->actingAs($secretaire)->get('/declarations')->assertStatus(200);
        $this->actingAs($secretaire)->get("/declarations/{$declaration->id}")->assertStatus(200);

        $this->actingAs($secretaire)->get('/declarations/create')->assertStatus(403);
        $this->actingAs($secretaire)->get("/declarations/{$declaration->id}/edit")->assertStatus(403);
        $this->actingAs($secretaire)->delete("/declarations/{$declaration->id}")->assertStatus(403);

        // Le télé-dépôt SIMPL lui est également refusé
        $this->actingAs($secretaire)->post("/declarations/{$declaration->id}/mark-filed", [
            'filing_date' => now()->toDateString(),
        ])->assertStatus(403);
    }

    /**
     * Test RG10 : la date effective de télétransmission SIMPL ne peut pas être postdatée.
     */
    public function test_declaration_filing_date_cannot_be_in_the_future(): void
    {
        $comptable = User::where('email', 'comptable1@cabinet.ma')->first();
        $declaration = Declaration::first();

        $future = $this->actingAs($comptable)->post("/declarations/{$declaration->id}/mark-filed", [
            'filing_date' => now()->addMonth()->toDateString(),
            'filing_reference' => 'SIMPL-TEST-001',
        ]);
        $future->assertSessionHasErrors('filing_date');

        $valid = $this->actingAs($comptable)->post("/declarations/{$declaration->id}/mark-filed", [
            'filing_date' => now()->toDateString(),
            'filing_reference' => 'SIMPL-TEST-001',
        ]);
        $valid->assertSessionHasNoErrors();
        $this->assertDatabaseHas('declarations', ['id' => $declaration->id, 'status' => 'deposee']);
    }

    /**
     * Test RBAC (matrice §4, Archivage GED) : le dépôt est ouvert à tous, mais la suppression
     * d'une pièce justificative reste réservée à l'Administrateur et au Gérant.
     */
    public function test_rbac_document_deletion_restricted_to_direction(): void
    {
        $comptable = User::where('email', 'comptable1@cabinet.ma')->first();
        $gerant = User::where('email', 'gerant@cabinet.ma')->first();
        $document = Document::first();

        $this->actingAs($comptable)->delete("/documents/{$document->id}")->assertStatus(403);
        $this->assertDatabaseHas('documents', ['id' => $document->id]);

        $this->actingAs($gerant)->delete("/documents/{$document->id}")->assertSessionHas('success');
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    /**
     * Test sécurité : un administrateur ne peut pas se verrouiller lui-même hors du système
     * (auto-rétrogradation ou auto-désactivation via le formulaire d'édition).
     */
    public function test_admin_cannot_lock_himself_out(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $roleSecretaire = Role::where('slug', 'secretaire')->first();

        // Tentative d'auto-rétrogradation
        $demotion = $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $roleSecretaire->id,
            'status' => 'actif',
        ]);
        $demotion->assertSessionHasErrors('role_id');

        // Tentative d'auto-désactivation
        $desactivation = $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role_id' => $admin->role_id,
            'status' => 'inactif',
        ]);
        $desactivation->assertSessionHasErrors('status');

        // Le compte reste administrateur et actif
        $admin->refresh();
        $this->assertTrue($admin->isAdmin());
        $this->assertSame('actif', $admin->status);
    }

    /**
     * Test sécurité : le système doit toujours conserver au moins un administrateur actif.
     */
    public function test_last_active_admin_cannot_be_removed(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $gerant = User::where('email', 'gerant@cabinet.ma')->first();
        $adminRoleId = Role::where('slug', 'admin')->value('id');

        // Le gérant est promu administrateur : il y a alors 2 administrateurs
        $this->actingAs($admin)->put("/users/{$gerant->id}", [
            'name' => $gerant->name,
            'email' => $gerant->email,
            'role_id' => $adminRoleId,
            'status' => 'actif',
        ]);
        $gerant->refresh();
        $this->assertTrue($gerant->isAdmin());

        // Rétrograder ce second administrateur est autorisé (il en reste un)
        $this->actingAs($admin)->put("/users/{$gerant->id}", [
            'name' => $gerant->name,
            'email' => $gerant->email,
            'role_id' => Role::where('slug', 'gerant')->value('id'),
            'status' => 'actif',
        ])->assertSessionHasNoErrors();

        // En revanche, supprimer le dernier administrateur actif est refusé
        $this->actingAs($gerant->fresh())->delete("/users/{$admin->id}")->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * Test intégrité comptable : un client rattaché à des pièces comptables ne peut pas
     * être supprimé (ses factures deviendraient orphelines et fausseraient les rapports).
     */
    public function test_client_with_accounting_history_cannot_be_deleted(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $clientAvecFactures = Client::has('invoices')->first();

        $refus = $this->actingAs($admin)->delete("/clients/{$clientAvecFactures->id}");
        $refus->assertSessionHas('error');
        $this->assertNotSoftDeleted('clients', ['id' => $clientAvecFactures->id]);

        // Un client sans aucune pièce comptable reste supprimable
        $clientVierge = Client::create([
            'type' => 'entreprise',
            'company_name' => 'Prospect Saisi Par Erreur SARL',
            'status' => 'prospect',
        ]);
        $this->actingAs($admin)->delete("/clients/{$clientVierge->id}")->assertSessionHas('success');
        $this->assertSoftDeleted('clients', ['id' => $clientVierge->id]);
    }

    /**
     * Test intégrité comptable : une facture conserve l'identité de son client
     * même si celui-ci se trouve en corbeille (rapports financiers toujours justes).
     */
    public function test_invoice_keeps_client_identity_when_client_is_trashed(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $invoice = Invoice::with('client')->first();
        $nomClient = $invoice->client->company_name;

        $invoice->client->delete();

        $invoiceRechargee = Invoice::with('client')->find($invoice->id);
        $this->assertNotNull($invoiceRechargee->client, "La facture a perdu son client.");
        $this->assertSame($nomClient, $invoiceRechargee->client->company_name);

        // L'export Excel reste exploitable
        $this->actingAs($admin)->post('/reports/generate', [
            'report_type' => 'financial',
            'format' => 'excel',
        ])->assertStatus(200);
    }

    /**
     * Test RG02 : une fiche supprimée reste consultable et restaurable depuis la corbeille.
     */
    public function test_deleted_client_can_be_restored_from_trash(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();

        // Un client sans pièce comptable rattachée (les autres sont protégés à la suppression)
        $client = Client::create([
            'type' => 'entreprise',
            'company_name' => 'Client À Restaurer SARL',
            'ice' => '001122334455667',
            'city' => 'Rabat',
            'status' => 'prospect',
        ]);

        $this->actingAs($admin)->delete("/clients/{$client->id}");
        $this->assertSoftDeleted('clients', ['id' => $client->id]);

        // La fiche apparaît dans la corbeille
        $trash = $this->actingAs($admin)->get('/corbeille');
        $trash->assertStatus(200);
        $trash->assertSee($client->company_name);

        // Restauration
        $restore = $this->actingAs($admin)->post("/corbeille/clients/{$client->id}/restaurer");
        $restore->assertSessionHas('success');
        $this->assertNotSoftDeleted('clients', ['id' => $client->id]);

        // L'opération est tracée (RG14)
        $this->assertDatabaseHas('activity_logs', ['action' => 'restauration', 'module' => 'clients']);
    }

    /**
     * Test RG02 : un collaborateur supprimé est réintégrable depuis la corbeille,
     * et les dossiers dont il était responsable conservent son identité.
     */
    public function test_deleted_employee_can_be_restored_and_keeps_his_assignments(): void
    {
        $admin = User::where('email', 'admin@cabinet.ma')->first();
        $employee = Employee::whereHas('responsibleDossiers')->first() ?? Employee::first();
        $dossier = $employee->responsibleDossiers()->first();

        $this->actingAs($admin)->delete("/employees/{$employee->id}");
        $this->assertSoftDeleted('employees', ['id' => $employee->id]);

        // Le dossier conserve le nom de son responsable même mis en corbeille
        if ($dossier) {
            $this->assertNotNull($dossier->fresh()->responsible);
            $this->assertSame($employee->full_name, $dossier->fresh()->responsible->full_name);
        }

        $trash = $this->actingAs($admin)->get('/corbeille');
        $trash->assertStatus(200);
        $trash->assertSee($employee->full_name);

        $this->actingAs($admin)
            ->post("/corbeille/collaborateurs/{$employee->id}/restaurer")
            ->assertSessionHas('success');

        $this->assertNotSoftDeleted('employees', ['id' => $employee->id]);
    }

    /**
     * Test RBAC : la corbeille est réservée à l'Administrateur et au Gérant.
     */
    public function test_trash_is_restricted_to_admin_and_gerant(): void
    {
        $comptable = User::where('email', 'comptable1@cabinet.ma')->first();
        $secretaire = User::where('email', 'secretaire@cabinet.ma')->first();
        $gerant = User::where('email', 'gerant@cabinet.ma')->first();

        $this->actingAs($comptable)->get('/corbeille')->assertStatus(403);
        $this->actingAs($secretaire)->get('/corbeille')->assertStatus(403);
        $this->actingAs($gerant)->get('/corbeille')->assertStatus(200);
    }

    /**
     * Test RBAC : la gestion des collaborateurs (données RH, salaire) est réservée
     * à l'Administrateur et au Gérant.
     */
    public function test_rbac_comptable_cannot_manage_employees(): void
    {
        $comptable = User::where('email', 'comptable1@cabinet.ma')->first();
        $employee = Employee::first();

        $resCreate = $this->actingAs($comptable)->get('/employees/create');
        $resCreate->assertStatus(403);

        $resDelete = $this->actingAs($comptable)->delete("/employees/{$employee->id}");
        $resDelete->assertStatus(403);
    }
}
