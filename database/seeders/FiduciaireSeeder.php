<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Deadline;
use App\Models\Declaration;
use App\Models\DeclarationType;
use App\Models\Document;
use App\Models\Dossier;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FiduciaireSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Rôles
        // Les descriptions reprennent fidèlement la matrice des droits
        // (docs/LIVRABLE_7_DOCUMENTATION_TECHNIQUE.md §4), appliquée par app/Policies.
        $roleAdmin = Role::create([
            'name' => 'Administrateur',
            'slug' => 'admin',
            'description' => 'Accès total : gestion des utilisateurs, journal d\'audit, corbeille et configuration du système.',
        ]);

        $roleGerant = Role::create([
            'name' => 'Gérant / Directeur',
            'slug' => 'gerant',
            'description' => 'Pilotage du cabinet : supervision des dossiers, validation financière, rapports, corbeille et journal d\'audit.',
        ]);

        $roleComptable = Role::create([
            'name' => 'Comptable',
            'slug' => 'comptable',
            'description' => 'Gestion des dossiers de mission, saisie et télé-dépôt SIMPL, émission des factures et encaissements. Ne peut ni créer ni supprimer un client.',
        ]);

        $roleSecretaire = Role::create([
            'name' => 'Assistant / Secrétaire',
            'slug' => 'secretaire',
            'description' => 'Accueil clients : création des fiches, dépôt de pièces dans la GED et planification des échéances. Aucun accès aux opérations financières ni aux déclarations fiscales.',
        ]);

        // 2. Utilisateurs
        $adminUser = User::create([
            'role_id' => $roleAdmin->id,
            'name' => 'Administrateur Système',
            'email' => 'admin@cabinet.ma',
            'password' => Hash::make('password'),
            'phone' => '+212 5 22 10 20 30',
            'status' => 'actif',
            'last_login_at' => now(),
        ]);

        $gerantUser = User::create([
            'role_id' => $roleGerant->id,
            'name' => 'Driss Bennani',
            'email' => 'gerant@cabinet.ma',
            'password' => Hash::make('password'),
            'phone' => '+212 6 61 11 22 33',
            'status' => 'actif',
            'last_login_at' => now()->subHours(2),
        ]);

        $comptable1User = User::create([
            'role_id' => $roleComptable->id,
            'name' => 'Sara Alami',
            'email' => 'comptable1@cabinet.ma',
            'password' => Hash::make('password'),
            'phone' => '+212 6 62 33 44 55',
            'status' => 'actif',
            'last_login_at' => now()->subHours(4),
        ]);

        $comptable2User = User::create([
            'role_id' => $roleComptable->id,
            'name' => 'Yassine Berrada',
            'email' => 'comptable2@cabinet.ma',
            'password' => Hash::make('password'),
            'phone' => '+212 6 63 55 66 77',
            'status' => 'actif',
            'last_login_at' => now()->subDays(1),
        ]);

        $secretaireUser = User::create([
            'role_id' => $roleSecretaire->id,
            'name' => 'Fatima Mansouri',
            'email' => 'secretaire@cabinet.ma',
            'password' => Hash::make('password'),
            'phone' => '+212 6 64 77 88 99',
            'status' => 'actif',
            'last_login_at' => now()->subDays(2),
        ]);

        // 3. Collaborateurs (Employees)
        $empGerant = Employee::create([
            'user_id' => $gerantUser->id,
            'matricule' => 'EMP-001',
            'cin' => 'BE458921',
            'first_name' => 'Driss',
            'last_name' => 'Bennani',
            'email' => 'gerant@cabinet.ma',
            'phone' => '+212 6 61 11 22 33',
            'address' => 'Bd d’Anfa, Casablanca',
            'position' => 'Expert-Comptable & Directeur Associé',
            'hire_date' => '2015-01-01',
            'salary' => 35000.00,
            'status' => 'actif',
            'notes' => 'Associé gérant du cabinet fiduciaire.',
        ]);

        $empSara = Employee::create([
            'user_id' => $comptable1User->id,
            'matricule' => 'EMP-002',
            'cin' => 'BK334455',
            'first_name' => 'Sara',
            'last_name' => 'Alami',
            'email' => 'comptable1@cabinet.ma',
            'phone' => '+212 6 62 33 44 55',
            'address' => 'Maârif, Casablanca',
            'position' => 'Comptable Senior & Fiscaliste',
            'hire_date' => '2018-04-15',
            'salary' => 14000.00,
            'status' => 'actif',
            'notes' => 'Responsable des dossiers d’audit et des liasses fiscales.',
        ]);

        $empYassine = Employee::create([
            'user_id' => $comptable2User->id,
            'matricule' => 'EMP-003',
            'cin' => 'BL667788',
            'first_name' => 'Yassine',
            'last_name' => 'Berrada',
            'email' => 'comptable2@cabinet.ma',
            'phone' => '+212 6 63 55 66 77',
            'address' => 'Bourgogne, Casablanca',
            'position' => 'Comptable Junior',
            'hire_date' => '2021-09-01',
            'salary' => 8500.00,
            'status' => 'actif',
            'notes' => 'En charge des déclarations TVA mensuelles et saisies.',
        ]);

        $empFatima = Employee::create([
            'user_id' => $secretaireUser->id,
            'matricule' => 'EMP-004',
            'cin' => 'BM889900',
            'first_name' => 'Fatima',
            'last_name' => 'Mansouri',
            'email' => 'secretaire@cabinet.ma',
            'phone' => '+212 6 64 77 88 99',
            'address' => 'Sidi Maârouf, Casablanca',
            'position' => 'Assistante de Direction & Facturation',
            'hire_date' => '2020-02-10',
            'salary' => 7500.00,
            'status' => 'actif',
            'notes' => 'Accueil, facturation, suivi des règlements clients et archivage GED.',
        ]);

        // 4. Clients
        $clientABC = Client::create([
            'type' => 'entreprise',
            'company_name' => 'ABC Consulting SARL',
            'trade_name' => 'ABC Consulting',
            'legal_form' => 'SARL',
            'ice' => '002134567890012',
            'if_number' => '45892100',
            'rc_number' => '124587',
            'patent_number' => '34215689',
            'cnss_number' => '7845123',
            'share_capital' => 100000.00,
            'activity' => 'Conseil en systèmes informatiques et gestion de projets digitaux',
            'address' => '14 Bd Mohammed V, 3ème étage',
            'city' => 'Casablanca',
            'country' => 'Maroc',
            'phone' => '+212 5 22 45 67 89',
            'email' => 'contact@abc-consulting.ma',
            'website' => 'https://www.abc-consulting.ma',
            'client_since' => '2021-03-15',
            'status' => 'actif',
            'notes' => 'Client régulier - Tenue comptable complète et conseil fiscal.',
            'created_by' => $adminUser->id,
        ]);

        ClientContact::create([
            'client_id' => $clientABC->id,
            'first_name' => 'Amine',
            'last_name' => 'Tazi',
            'cin' => 'A123456',
            'position' => 'Gérant Unique',
            'phone' => '+212 6 61 23 45 67',
            'email' => 'amine.tazi@abc-consulting.ma',
            'is_primary' => true,
        ]);

        $clientAtlas = Client::create([
            'type' => 'entreprise',
            'company_name' => 'Atlas Distribution SARL',
            'trade_name' => 'Atlas Négoce',
            'legal_form' => 'SARL',
            'ice' => '001987654321045',
            'if_number' => '33445566',
            'rc_number' => '98765',
            'patent_number' => '45123678',
            'cnss_number' => '9658231',
            'share_capital' => 500000.00,
            'activity' => 'Importation et distribution de matériel industriel et quincaillerie',
            'address' => 'Zone Industrielle Aïn Sebaâ, Rue 4',
            'city' => 'Casablanca',
            'country' => 'Maroc',
            'phone' => '+212 5 22 35 12 80',
            'email' => 'direction@atlas-distribution.ma',
            'website' => 'https://www.atlas-distribution.ma',
            'client_since' => '2019-06-01',
            'status' => 'actif',
            'notes' => 'Gros volume de factures mensuelles, suivi des stocks et déclarations douanières.',
            'created_by' => $gerantUser->id,
        ]);

        ClientContact::create([
            'client_id' => $clientAtlas->id,
            'first_name' => 'Salma',
            'last_name' => 'Idrissi',
            'cin' => 'B789456',
            'position' => 'Directrice Générale',
            'phone' => '+212 6 62 98 76 54',
            'email' => 's.idrissi@atlas-distribution.ma',
            'is_primary' => true,
        ]);

        $clientMarocDigital = Client::create([
            'type' => 'entreprise',
            'company_name' => 'Maroc Digital SARL',
            'trade_name' => 'MD Agency',
            'legal_form' => 'SARL AU',
            'ice' => '003456789012078',
            'if_number' => '55667788',
            'rc_number' => '234561',
            'patent_number' => '23568912',
            'cnss_number' => '8523697',
            'share_capital' => 200000.00,
            'activity' => 'Agence de communication digitale, marketing et création de contenu',
            'address' => 'Avenue Allal Ben Abdellah',
            'city' => 'Rabat',
            'country' => 'Maroc',
            'phone' => '+212 5 37 70 12 34',
            'email' => 'bonjour@marocdigital.ma',
            'website' => 'https://www.marocdigital.ma',
            'client_since' => '2022-11-20',
            'status' => 'actif',
            'notes' => 'Start-up dynamique éligible aux avantages fiscaux statut Jeune Entreprise Innovante.',
            'created_by' => $adminUser->id,
        ]);

        ClientContact::create([
            'client_id' => $clientMarocDigital->id,
            'first_name' => 'Karim',
            'last_name' => 'Benjelloun',
            'cin' => 'C456789',
            'position' => 'Directeur des Opérations',
            'phone' => '+212 6 63 12 34 56',
            'email' => 'karim@marocdigital.ma',
            'is_primary' => true,
        ]);

        $clientNova = Client::create([
            'type' => 'entreprise',
            'company_name' => 'Nova Services SARL',
            'trade_name' => 'Nova Services',
            'legal_form' => 'SARL',
            'ice' => '004567890123099',
            'if_number' => '66778899',
            'rc_number' => '345672',
            'patent_number' => '12457896',
            'cnss_number' => '6547891',
            'share_capital' => 50000.00,
            'activity' => 'Services de conciergerie d’entreprise, nettoyage et maintenance',
            'address' => 'Quartier Palmier, Rue Jean Jaurès',
            'city' => 'Casablanca',
            'country' => 'Maroc',
            'phone' => '+212 5 22 20 40 60',
            'email' => 'contact@novaservices.ma',
            'client_since' => '2024-01-10',
            'status' => 'prospect',
            'notes' => 'En cours de finalisation du contrat d’assistance comptable.',
            'created_by' => $secretaireUser->id,
        ]);

        ClientContact::create([
            'client_id' => $clientNova->id,
            'first_name' => 'Houda',
            'last_name' => 'Chraibi',
            'cin' => 'D987123',
            'position' => 'Gérante',
            'phone' => '+212 6 64 56 78 90',
            'email' => 'houda.chraibi@novaservices.ma',
            'is_primary' => true,
        ]);

        // 5. Types de déclarations fiscales (Maroc)
        $tvaType = DeclarationType::create([
            'code' => 'TVA',
            'name' => 'Déclaration de TVA (Taxe sur la Valeur Ajoutée)',
            'periodicity' => 'trimestrielle',
            'description' => 'Déclaration trimestrielle ou mensuelle de TVA selon le chiffre d’affaires.',
        ]);

        $isType = DeclarationType::create([
            'code' => 'IS',
            'name' => 'Impôt sur les Sociétés (IS / Acomptes & Solde)',
            'periodicity' => 'trimestrielle',
            'description' => 'Acomptes trimestriels provisionnels et déclaration du résultat fiscal annuel.',
        ]);

        $irType = DeclarationType::create([
            'code' => 'IR',
            'name' => 'Impôt sur le Revenu / Salaires (IR)',
            'periodicity' => 'mensuelle',
            'description' => 'Déclaration mensuelle des retenues à la source sur traitements et salaires.',
        ]);

        $cnssType = DeclarationType::create([
            'code' => 'CNSS',
            'name' => 'Bordereau de Paiement des Cotisations CNSS',
            'periodicity' => 'mensuelle',
            'description' => 'Déclaration des salaires et cotisations patronales/salariales à la CNSS.',
        ]);

        $etat9421 = DeclarationType::create([
            'code' => 'ETAT_9421',
            'name' => 'État des Rémunérations Versées à des Tiers (9421)',
            'periodicity' => 'annuelle',
            'description' => 'Déclaration annuelle des commissions, courtages, vacations et honoraires.',
        ]);

        // 6. Dossiers
        $dossierABC = Dossier::create([
            'reference' => 'DOS-2026-0001',
            'client_id' => $clientABC->id,
            'type' => 'comptabilite',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'en_cours',
            'priority' => 'haute',
            'responsible_id' => $empSara->id,
            'description' => 'Tenue comptable régulière de l’exercice 2026 et élaboration de la liasse fiscale.',
            'notes' => 'Saisie mensuelle des journaux banque, caisse et achats.',
        ]);
        $dossierABC->employees()->attach($empSara->id, ['role_in_dossier' => 'Comptable référent', 'assigned_date' => '2026-01-01', 'status' => 'actif']);
        $dossierABC->employees()->attach($empYassine->id, ['role_in_dossier' => 'Saisie & Rapprochement', 'assigned_date' => '2026-01-01', 'status' => 'actif']);

        $dossierAtlas = Dossier::create([
            'reference' => 'DOS-2026-0002',
            'client_id' => $clientAtlas->id,
            'type' => 'fiscalite',
            'start_date' => '2026-01-15',
            'end_date' => '2026-12-31',
            'status' => 'en_cours',
            'priority' => 'urgente',
            'responsible_id' => $empSara->id,
            'description' => 'Gestion de la fiscalité d’entreprise, déclarations mensuelles de TVA et acomptes IS.',
            'notes' => 'Attention aux régimes suspensifs de TVA sur les importations.',
        ]);
        $dossierAtlas->employees()->attach($empSara->id, ['role_in_dossier' => 'Responsable fiscal', 'assigned_date' => '2026-01-15', 'status' => 'actif']);

        $dossierMarocDigital = Dossier::create([
            'reference' => 'DOS-2026-0003',
            'client_id' => $clientMarocDigital->id,
            'type' => 'conseil',
            'start_date' => '2026-02-01',
            'end_date' => '2026-06-30',
            'status' => 'en_cours',
            'priority' => 'moyenne',
            'responsible_id' => $empGerant->id,
            'description' => 'Conseil en restructuration financière et optimisation de la gestion de trésorerie.',
        ]);
        $dossierMarocDigital->employees()->attach($empGerant->id, ['role_in_dossier' => 'Expert conseil', 'assigned_date' => '2026-02-01', 'status' => 'actif']);

        $dossierNova = Dossier::create([
            'reference' => 'DOS-2026-0004',
            'client_id' => $clientNova->id,
            'type' => 'creation_entreprise',
            'start_date' => '2026-01-05',
            'end_date' => '2026-03-31',
            'status' => 'termine',
            'priority' => 'moyenne',
            'responsible_id' => $empFatima->id,
            'description' => 'Formalités juridiques de constitution de société, certificat négatif, statuts et RC.',
        ]);
        $dossierNova->employees()->attach($empFatima->id, ['role_in_dossier' => 'Formaliste', 'assigned_date' => '2026-01-05', 'status' => 'termine']);
        $dossierNova->employees()->attach($empSara->id, ['role_in_dossier' => 'Validation juridique', 'assigned_date' => '2026-01-05', 'status' => 'termine']);

        // 7. Déclarations Fiscales
        // Déclaration TVA 4ème trimestre 2025 (Déposée)
        Declaration::create([
            'client_id' => $clientABC->id,
            'dossier_id' => $dossierABC->id,
            'declaration_type_id' => $tvaType->id,
            'period' => 'T4 2025',
            'due_date' => Carbon::parse('2026-01-20'),
            'filing_date' => Carbon::parse('2026-01-18'),
            'status' => 'deposee',
            'amount' => 18200.00,
            'filing_reference' => 'SIMPL-TVA-2026-0489',
            'responsible_id' => $empSara->id,
            'comments' => 'Télédéclaration validée avec succès sur le portail SIMPL-TVA.',
        ]);

        // Déclaration TVA 1er trimestre 2026 (À venir / À préparer)
        Declaration::create([
            'client_id' => $clientABC->id,
            'dossier_id' => $dossierABC->id,
            'declaration_type_id' => $tvaType->id,
            'period' => 'T1 2026',
            'due_date' => Carbon::now()->addDays(20),
            'status' => 'a_preparer',
            'amount' => 14500.00,
            'responsible_id' => $empSara->id,
            'comments' => 'En attente des dernières factures de vente de Mars.',
        ]);

        // Acompte IS 1er Trimestre 2026 (En préparation)
        Declaration::create([
            'client_id' => $clientAtlas->id,
            'dossier_id' => $dossierAtlas->id,
            'declaration_type_id' => $isType->id,
            'period' => '1er Acompte IS 2026',
            'due_date' => Carbon::now()->addDays(5),
            'status' => 'en_preparation',
            'amount' => 25000.00,
            'responsible_id' => $empSara->id,
            'comments' => 'Calcul basé sur l’IS de l’exercice 2025 (25% du montant dû).',
        ]);

        // Déclaration IR Février 2026 (En retard - RG11)
        Declaration::create([
            'client_id' => $clientAtlas->id,
            'dossier_id' => $dossierAtlas->id,
            'declaration_type_id' => $irType->id,
            'period' => 'Février 2026',
            'due_date' => Carbon::now()->subDays(10),
            'status' => 'en_retard',
            'amount' => 6400.00,
            'responsible_id' => $empYassine->id,
            'comments' => 'Retard causé par une modification de la grille des primes du personnel.',
        ]);

        // Déclaration CNSS Février 2026 (Prête)
        Declaration::create([
            'client_id' => $clientMarocDigital->id,
            'dossier_id' => $dossierMarocDigital->id,
            'declaration_type_id' => $cnssType->id,
            'period' => 'Février 2026',
            'due_date' => Carbon::now()->addDays(3),
            'status' => 'prete',
            'amount' => 8900.00,
            'responsible_id' => $empYassine->id,
            'comments' => 'Bordereau prêt à être télétransmis sur le portail DAMANCOM.',
        ]);

        // 8. Factures et Lignes de Facture
        // Facture 1 : Payée intégralement
        $fac1 = Invoice::create([
            'reference' => 'FAC-2026-0001',
            'client_id' => $clientABC->id,
            'invoice_date' => Carbon::parse('2026-01-10'),
            'due_date' => Carbon::parse('2026-02-10'),
            'description' => 'Honoraires de tenue comptable et déclarations fiscales - T1 2026',
            'status' => 'emise',
            'created_by' => $secretaireUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $fac1->id,
            'description' => 'Tenue de comptabilité mensuelle (Janvier - Mars 2026)',
            'quantity' => 3,
            'unit_price' => 1500.00,
            'tax_rate' => 20.00,
        ]);
        InvoiceItem::create([
            'invoice_id' => $fac1->id,
            'description' => 'Établissement de la déclaration TVA trimestrielle',
            'quantity' => 1,
            'unit_price' => 500.00,
            'tax_rate' => 20.00,
        ]);
        $fac1->recalculateTotals();

        Payment::create([
            'invoice_id' => $fac1->id,
            'client_id' => $clientABC->id,
            'payment_date' => Carbon::parse('2026-01-25'),
            'amount' => $fac1->total_ttc,
            'payment_method' => 'virement',
            'reference' => 'VIR-AWB-897451',
            'bank' => 'Attijariwafa Bank',
            'comments' => 'Règlement total reçu par virement bancaire.',
            'created_by' => $secretaireUser->id,
        ]);

        // Facture 2 : Partiellement payée
        $fac2 = Invoice::create([
            'reference' => 'FAC-2026-0002',
            'client_id' => $clientAtlas->id,
            'invoice_date' => Carbon::parse('2026-01-20'),
            'due_date' => Carbon::parse('2026-02-28'),
            'description' => 'Arrêté des comptes annuels 2025 et liasse fiscale',
            'status' => 'emise',
            'created_by' => $secretaireUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $fac2->id,
            'description' => 'Élaboration de la liasse fiscale et bilan comptable 2025',
            'quantity' => 1,
            'unit_price' => 10000.00,
            'tax_rate' => 20.00,
        ]);
        InvoiceItem::create([
            'invoice_id' => $fac2->id,
            'description' => 'Dépôt légal au greffe du tribunal de commerce',
            'quantity' => 1,
            'unit_price' => 2000.00,
            'tax_rate' => 20.00,
        ]);
        $fac2->recalculateTotals();

        Payment::create([
            'invoice_id' => $fac2->id,
            'client_id' => $clientAtlas->id,
            'payment_date' => Carbon::parse('2026-02-15'),
            'amount' => 7000.00,
            'payment_method' => 'cheque',
            'reference' => 'CHQ-BCP-554123',
            'bank' => 'Banque Populaire (BCP)',
            'comments' => 'Acompte de 7 000 DH versé par chèque. Solde convenu sous 30 jours.',
            'created_by' => $secretaireUser->id,
        ]);

        // Facture 3 : Émise (Non payée, dans les délais)
        $fac3 = Invoice::create([
            'reference' => 'FAC-2026-0003',
            'client_id' => $clientMarocDigital->id,
            'invoice_date' => Carbon::now()->subDays(5),
            'due_date' => Carbon::now()->addDays(25),
            'description' => 'Mission de conseil financier et élaboration du Business Plan',
            'status' => 'emise',
            'created_by' => $secretaireUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $fac3->id,
            'description' => 'Diagnostic de gestion et prévisionnel financier sur 3 ans',
            'quantity' => 1,
            'unit_price' => 8000.00,
            'tax_rate' => 20.00,
        ]);
        $fac3->recalculateTotals();

        // Facture 4 : En retard
        $fac4 = Invoice::create([
            'reference' => 'FAC-2026-0004',
            'client_id' => $clientNova->id,
            'invoice_date' => Carbon::now()->subDays(45),
            'due_date' => Carbon::now()->subDays(15),
            'description' => 'Pack Création d’entreprise SARL - Honoraires et débours',
            'status' => 'en_retard',
            'created_by' => $secretaireUser->id,
        ]);
        InvoiceItem::create([
            'invoice_id' => $fac4->id,
            'description' => 'Rédaction des statuts et formalités au CRI / Tribunal',
            'quantity' => 1,
            'unit_price' => 4000.00,
            'tax_rate' => 20.00,
        ]);
        $fac4->recalculateTotals();

        // 9. Documents GED
        Document::create([
            'title' => 'Statuts constitutifs de société',
            'category' => 'statuts',
            'client_id' => $clientABC->id,
            'dossier_id' => $dossierABC->id,
            'file_path' => 'documents/statuts_abc_consulting.pdf',
            'file_name' => 'statuts_abc_consulting.pdf',
            'file_size' => 1450000,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'uploaded_by' => $comptable1User->id,
            'notes' => 'Statuts originaux signés et enregistrés auprès de l’administration fiscale.',
        ]);

        Document::create([
            'title' => 'Modèle J - Registre du Commerce',
            'category' => 'rc',
            'client_id' => $clientAtlas->id,
            'dossier_id' => $dossierAtlas->id,
            'file_path' => 'documents/rc_atlas_distribution.pdf',
            'file_name' => 'rc_atlas_distribution.pdf',
            'file_size' => 520000,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'uploaded_by' => $comptable1User->id,
            'notes' => 'Extrait récent du registre de commerce délivré par le Tribunal de Commerce.',
        ]);

        Document::create([
            'title' => 'Attestation d’immatriculation fiscale (IF & ICE)',
            'category' => 'attestation',
            'client_id' => $clientMarocDigital->id,
            'dossier_id' => $dossierMarocDigital->id,
            'file_path' => 'documents/attestation_fiscale_md.pdf',
            'file_name' => 'attestation_fiscale_md.pdf',
            'file_size' => 380000,
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'uploaded_by' => $adminUser->id,
            'notes' => 'Bulletin de notification des identifiants fiscaux.',
        ]);

        // Générer les fichiers physiques PDF valides pour les documents de démonstration
        $demoDocs = [
            'attestation_fiscale_md.pdf' => 'ATTESTATION D’IMMATRICULATION FISCALE - MAROC DIGITAL SARL',
            'rc_atlas_distribution.pdf' => 'EXTRAIT DU REGISTRE DU COMMERCE (MODÈLE J) - ATLAS DISTRIBUTION',
            'statuts_abc_consulting.pdf' => 'STATUTS CONSTITUTIFS DE SOCIÉTÉ - ABC CONSULTING SARL',
        ];
        foreach ($demoDocs as $fileName => $docTitle) {
            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . $docTitle . '</title><style>body{font-family:DejaVu Sans,sans-serif;padding:30px;color:#1f2937;line-height:1.6;}.h{border-bottom:2px solid #1e40af;padding-bottom:12px;margin-bottom:20px;}.h h1{color:#1e40af;font-size:18px;margin:0;}.box{background:#f3f4f6;border-left:4px solid #1e40af;padding:15px;margin-top:20px;}</style></head><body><div class="h"><p style="color:#6b7280;margin:0;font-size:12px;">ROYAUME DU MAROC — GESTION ÉLECTRONIQUE DES DOCUMENTS</p><h1>' . $docTitle . '</h1></div><div class="box"><p><strong>Document Officiel Enregistré</strong></p><p>Ce document est une pièce justificative officielle versée au dossier permanent du client dans le système FiducialPro.</p><p>Certifié conforme et intègre par le cabinet fiduciaire.</p></div></body></html>';
            $pdfContent = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->output();
            \Illuminate\Support\Facades\Storage::disk('local')->put('documents/' . $fileName, $pdfContent);
            $pubPath = storage_path('app/public/documents');
            if (!is_dir($pubPath)) @mkdir($pubPath, 0777, true);
            @file_put_contents($pubPath . '/' . $fileName, $pdfContent);
            $privPath = storage_path('app/private/documents');
            if (!is_dir($privPath)) @mkdir($privPath, 0777, true);
            @file_put_contents($privPath . '/' . $fileName, $pdfContent);
        }

        // 10. Échéances (Calendrier)
        Deadline::create([
            'title' => 'Déclaration TVA 1er Trimestre 2026',
            'type' => 'fiscal',
            'client_id' => $clientABC->id,
            'dossier_id' => $dossierABC->id,
            'due_date' => Carbon::now()->addDays(20),
            'priority' => 'haute',
            'responsible_id' => $empSara->id,
            'status' => 'en_attente',
            'description' => 'Télédéclaration et paiement TVA avant le 20 du mois.',
        ]);

        Deadline::create([
            'title' => '1er Acompte provisionnel IS 2026',
            'type' => 'fiscal',
            'client_id' => $clientAtlas->id,
            'dossier_id' => $dossierAtlas->id,
            'due_date' => Carbon::now()->addDays(5),
            'priority' => 'urgente',
            'responsible_id' => $empSara->id,
            'status' => 'en_cours',
            'description' => 'Échéance légale du 1er acompte de l’impôt sur les sociétés.',
        ]);

        Deadline::create([
            'title' => 'Relance Facture impayée FAC-2026-0004',
            'type' => 'paiement',
            'client_id' => $clientNova->id,
            'due_date' => Carbon::now()->subDays(2),
            'priority' => 'urgente',
            'responsible_id' => $empFatima->id,
            'status' => 'en_retard',
            'description' => 'Appel téléphonique de relance client pour la facture en retard.',
        ]);

        // 11. Notifications
        Notification::create([
            'user_id' => $adminUser->id,
            'title' => 'Déclaration en retard',
            'message' => 'La déclaration IR Février 2026 pour Atlas Distribution SARL a dépassé son échéance.',
            'type' => 'danger',
            'link' => '/declarations',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $gerantUser->id,
            'title' => 'Facture impayée',
            'message' => 'La facture FAC-2026-0004 de Nova Services SARL (4 800,00 DH) est arrivée à échéance.',
            'type' => 'warning',
            'link' => '/invoices',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $secretaireUser->id,
            'title' => 'Paiement enregistré',
            'message' => 'Un paiement de 6 000,00 DH a été comptabilisé sur la facture FAC-2026-0001 (ABC Consulting).',
            'type' => 'success',
            'link' => '/payments',
            'is_read' => true,
            'read_at' => now()->subDay(),
        ]);

        // 12. Journal d'Audit (ActivityLog)
        ActivityLog::create([
            'user_id' => $adminUser->id,
            'user_name' => $adminUser->name,
            'action' => 'initialisation',
            'module' => 'system',
            'description' => 'Initialisation du système de gestion fiduciaire et paramétrage initial.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subDays(3),
        ]);

        ActivityLog::create([
            'user_id' => $gerantUser->id,
            'user_name' => $gerantUser->name,
            'action' => 'creation',
            'module' => 'clients',
            'target_id' => $clientABC->id,
            'target_label' => $clientABC->company_name,
            'description' => 'Création de la fiche client ABC Consulting SARL (ICE 002134567890012).',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subDays(2),
        ]);

        ActivityLog::create([
            'user_id' => $secretaireUser->id,
            'user_name' => $secretaireUser->name,
            'action' => 'creation',
            'module' => 'invoices',
            'target_id' => $fac1->id,
            'target_label' => $fac1->reference,
            'description' => 'Émission de la facture FAC-2026-0001 d’un montant de 6 000,00 DH TTC.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subDay(),
        ]);

        ActivityLog::create([
            'user_id' => $secretaireUser->id,
            'user_name' => $secretaireUser->name,
            'action' => 'paiement',
            'module' => 'payments',
            'target_id' => $fac1->id,
            'target_label' => $fac1->reference,
            'description' => 'Enregistrement du règlement total de 6 000,00 DH par virement pour la facture FAC-2026-0001.',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now()->subHours(5),
        ]);
    }
}
