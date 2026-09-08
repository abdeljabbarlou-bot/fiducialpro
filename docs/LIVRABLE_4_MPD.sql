-- =============================================================================
-- LIVRABLE 4 : MODÈLE PHYSIQUE DES DONNÉES (MPD - SQL DDL)
-- Système de Gestion d'un Cabinet Fiduciaire (FiducialPro)
-- Contexte : Droit des affaires et fiscalité marocaine (ICE, IF, RC, CNSS, TVA, IS)
-- =============================================================================

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS deadlines;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS invoice_items;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS declarations;
DROP TABLE IF EXISTS declaration_types;
DROP TABLE IF EXISTS dossier_employees;
DROP TABLE IF EXISTS dossiers;
DROP TABLE IF EXISTS client_contacts;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS roles;

-- -----------------------------------------------------------------------------
-- 1. Table : roles (RBAC - Contrôle d'accès basé sur les rôles)
-- -----------------------------------------------------------------------------
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Table : users (Comptes d'authentification applicatifs)
-- -----------------------------------------------------------------------------
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    status ENUM('actif', 'inactif') NOT NULL DEFAULT 'actif',
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Table : employees (Collaborateurs et personnel du cabinet)
-- -----------------------------------------------------------------------------
CREATE TABLE employees (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL UNIQUE,
    matricule VARCHAR(30) NOT NULL UNIQUE,
    cin VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address TEXT NULL,
    position VARCHAR(100) NOT NULL,
    hire_date DATE NOT NULL,
    salary DECIMAL(10, 2) NULL,
    status ENUM('actif', 'inactif', 'conge') NOT NULL DEFAULT 'actif',
    notes TEXT NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_employees_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_emp_status (status),
    INDEX idx_emp_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. Table : clients (Entreprises clientes et personnes physiques)
-- -----------------------------------------------------------------------------
CREATE TABLE clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('entreprise', 'particulier') NOT NULL DEFAULT 'entreprise',
    company_name VARCHAR(255) NOT NULL,
    trade_name VARCHAR(255) NULL,
    legal_form VARCHAR(50) NULL,
    ice VARCHAR(20) NULL,
    if_number VARCHAR(30) NULL,
    rc_number VARCHAR(30) NULL,
    patent_number VARCHAR(30) NULL,
    cnss_number VARCHAR(30) NULL,
    share_capital DECIMAL(15, 2) NULL DEFAULT 0.00,
    activity TEXT NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL DEFAULT 'Casablanca',
    country VARCHAR(100) NULL DEFAULT 'Maroc',
    phone VARCHAR(30) NULL,
    email VARCHAR(191) NULL,
    website VARCHAR(255) NULL,
    status ENUM('actif', 'prospect', 'suspendu', 'archive') NOT NULL DEFAULT 'actif',
    notes TEXT NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clients_ice (ice),
    INDEX idx_clients_if (if_number),
    INDEX idx_clients_rc (rc_number),
    INDEX idx_clients_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. Table : client_contacts (Représentants légaux, gérants et contacts)
-- -----------------------------------------------------------------------------
CREATE TABLE client_contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    cin VARCHAR(20) NULL,
    position VARCHAR(100) NULL,
    phone VARCHAR(30) NULL,
    email VARCHAR(191) NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_contacts_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. Table : dossiers (Missions comptables, fiscales et juridiques)
-- -----------------------------------------------------------------------------
CREATE TABLE dossiers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(30) NOT NULL UNIQUE,
    client_id BIGINT UNSIGNED NOT NULL,
    responsible_id BIGINT UNSIGNED NULL,
    type ENUM('comptabilite', 'fiscalite', 'conseil', 'formation', 'social_rh', 'juridique', 'creation_entreprise') NOT NULL DEFAULT 'comptabilite',
    description TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    status ENUM('nouveau', 'en_cours', 'en_attente', 'termine', 'suspendu', 'archive') NOT NULL DEFAULT 'nouveau',
    priority ENUM('faible', 'moyenne', 'haute', 'urgente') NOT NULL DEFAULT 'moyenne',
    notes TEXT NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_dossiers_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE,
    CONSTRAINT fk_dossiers_responsible FOREIGN KEY (responsible_id) REFERENCES employees (id) ON DELETE SET NULL,
    INDEX idx_dossiers_status (status),
    INDEX idx_dossiers_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. Table : dossier_employees (Affectation des collaborateurs N:N - RG03, RG04)
-- -----------------------------------------------------------------------------
CREATE TABLE dossier_employees (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dossier_id BIGINT UNSIGNED NOT NULL,
    employee_id BIGINT UNSIGNED NOT NULL,
    role_in_dossier VARCHAR(100) NOT NULL DEFAULT 'Comptable référent',
    assigned_date DATE NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_dossier_emp_dossier FOREIGN KEY (dossier_id) REFERENCES dossiers (id) ON DELETE CASCADE,
    CONSTRAINT fk_dossier_emp_employee FOREIGN KEY (employee_id) REFERENCES employees (id) ON DELETE CASCADE,
    UNIQUE KEY uq_dossier_employee (dossier_id, employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. Table : declaration_types (Obligations fiscales légales paramétrées)
-- -----------------------------------------------------------------------------
CREATE TABLE declaration_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(30) NOT NULL UNIQUE,
    periodicity ENUM('mensuelle', 'trimestrielle', 'annuelle', 'ponctuelle') NOT NULL DEFAULT 'trimestrielle',
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. Table : declarations (Déclarations fiscales et sociales SIMPL - RG11)
-- -----------------------------------------------------------------------------
CREATE TABLE declarations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NOT NULL,
    dossier_id BIGINT UNSIGNED NULL,
    declaration_type_id BIGINT UNSIGNED NOT NULL,
    responsible_id BIGINT UNSIGNED NULL,
    period VARCHAR(50) NOT NULL,
    due_date DATE NOT NULL,
    filing_date DATE NULL,
    amount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    status ENUM('a_preparer', 'en_preparation', 'prete', 'deposee', 'payee', 'en_retard', 'annulee') NOT NULL DEFAULT 'a_preparer',
    filing_reference VARCHAR(100) NULL,
    comments TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_decl_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE,
    CONSTRAINT fk_decl_dossier FOREIGN KEY (dossier_id) REFERENCES dossiers (id) ON DELETE SET NULL,
    CONSTRAINT fk_decl_type FOREIGN KEY (declaration_type_id) REFERENCES declaration_types (id) ON DELETE RESTRICT,
    CONSTRAINT fk_decl_responsible FOREIGN KEY (responsible_id) REFERENCES employees (id) ON DELETE SET NULL,
    INDEX idx_decl_status (status),
    INDEX idx_decl_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. Table : invoices (Factures d'honoraires - RG06, RG07, RG08)
-- -----------------------------------------------------------------------------
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(30) NOT NULL UNIQUE,
    client_id BIGINT UNSIGNED NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal_ht DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    total_ttc DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    paid_amount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    remaining_amount DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    status ENUM('brouillon', 'emise', 'partiellement_payee', 'payee', 'en_retard', 'annulee') NOT NULL DEFAULT 'emise',
    payment_conditions VARCHAR(255) NULL,
    description TEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoices_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE RESTRICT,
    INDEX idx_invoices_status (status),
    INDEX idx_invoices_due_date (due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. Table : invoice_items (Lignes de prestations et débours)
-- -----------------------------------------------------------------------------
CREATE TABLE invoice_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(8, 2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    tax_rate DECIMAL(5, 2) NOT NULL DEFAULT 20.00,
    total_ht DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    total_ttc DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. Table : payments (Règlements d'honoraires et encaissements - RG08)
-- -----------------------------------------------------------------------------
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NULL,
    amount DECIMAL(12, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('virement', 'cheque', 'especes', 'carte', 'autre') NOT NULL DEFAULT 'virement',
    reference VARCHAR(100) NULL,
    bank VARCHAR(100) NULL,
    comments TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE RESTRICT,
    CONSTRAINT fk_payments_user FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_payments_date (payment_date),
    INDEX idx_payments_method (payment_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. Table : documents (GED - Gestion Électronique des Documents - RG12)
-- -----------------------------------------------------------------------------
CREATE TABLE documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NULL,
    dossier_id BIGINT UNSIGNED NULL,
    uploaded_by BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    file_size BIGINT NOT NULL,
    category ENUM('contrat', 'facture', 'releve_bancaire', 'declaration_fiscale', 'bilan', 'pv', 'rc', 'cin', 'statuts', 'attestation', 'document_comptable', 'autre') NOT NULL DEFAULT 'document_comptable',
    notes TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_docs_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE,
    CONSTRAINT fk_docs_dossier FOREIGN KEY (dossier_id) REFERENCES dossiers (id) ON DELETE CASCADE,
    CONSTRAINT fk_docs_user FOREIGN KEY (uploaded_by) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_docs_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. Table : deadlines (Calendrier des échéances et jalons)
-- -----------------------------------------------------------------------------
CREATE TABLE deadlines (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED NULL,
    dossier_id BIGINT UNSIGNED NULL,
    responsible_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    due_date DATE NOT NULL,
    type ENUM('fiscal', 'social', 'comptable', 'paiement', 'juridique', 'autre') NOT NULL DEFAULT 'fiscal',
    status ENUM('en_attente', 'en_cours', 'terminee', 'annulee') NOT NULL DEFAULT 'en_attente',
    priority ENUM('faible', 'moyenne', 'haute', 'urgente') NOT NULL DEFAULT 'moyenne',
    reminder_days INT NOT NULL DEFAULT 3,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_deadlines_client FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE CASCADE,
    CONSTRAINT fk_deadlines_dossier FOREIGN KEY (dossier_id) REFERENCES dossiers (id) ON DELETE CASCADE,
    CONSTRAINT fk_deadlines_responsible FOREIGN KEY (responsible_id) REFERENCES employees (id) ON DELETE SET NULL,
    INDEX idx_deadlines_due_date (due_date),
    INDEX idx_deadlines_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. Table : activity_logs (Journal d'audit et traçabilité de sécurité - RG14)
-- -----------------------------------------------------------------------------
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    user_name VARCHAR(150) NOT NULL DEFAULT 'Système',
    module VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    target_id BIGINT UNSIGNED NULL,
    target_label VARCHAR(255) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_logs_module (module),
    INDEX idx_logs_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 16. Table : notifications (Système d'alertes internes)
-- -----------------------------------------------------------------------------
CREATE TABLE notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger') NOT NULL DEFAULT 'info',
    link VARCHAR(255) NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_notif_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
