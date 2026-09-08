# LIVRABLE 3 : MODÈLE LOGIQUE DES DONNÉES (MLD)

**Projet :** Système de Gestion d’un Cabinet Fiduciaire (FiducialPro)  
**Normalisation :** 3ème Forme Normale (3NF)  

---

## 1. Règles de Passage du MCD au MLD

1. **Entité simple :** Chaque entité du MCD devient une relation (table). L'identifiant devient la clé primaire (**PK**).
2. **Relation binaire aux cardinalités (1,1) - (0,N) :** La clé primaire de l'entité côté (0,N) migre dans la table côté (1,1) en tant que clé étrangère (**FK**). Exemple : `client_id` migre dans `dossiers`.
3. **Relation binaire ou N-aire aux cardinalités (0,N) - (0,N) :** La relation devient une table de liaison (pivot) ayant pour clé primaire composite la concaténation des identifiants des entités reliées. Exemple : `dossier_employees(dossier_id, employee_id)`.
4. **Relation réflexive ou 1:1 :** La clé étrangère migre avec contrainte `UNIQUE` (Ex: `users.employee_id`).

---

## 2. Schéma Relationnel Textuel Normalisé (3NF)

*Légende : **PK** = Clé Primaire, **FK** = Clé Étrangère (#)*

1. **ROLES** (
    **id** [PK],
    name,
    slug [UNIQUE],
    description,
    created_at,
    updated_at
)

2. **USERS** (
    **id** [PK],
    name,
    email [UNIQUE],
    password,
    phone,
    status,
    last_login_at,
    #role_id [FK -> ROLES.id],
    remember_token,
    created_at,
    updated_at
)

3. **EMPLOYEES** (
    **id** [PK],
    #user_id [FK -> USERS.id, NULLABLE, UNIQUE, ON DELETE SET NULL],
    matricule [UNIQUE],
    cin [UNIQUE],
    first_name,
    last_name,
    email,
    phone,
    address,
    position,
    hire_date,
    salary,
    status,
    notes,
    deleted_at,
    created_at,
)

4. **CLIENTS** (
    **id** [PK],
    type,
    company_name,
    trade_name,
    legal_form,
    ice [INDEX],
    if_number [INDEX],
    rc_number [INDEX],
    patent_number,
    cnss_number,
    share_capital,
    activity,
    address,
    city,
    country,
    phone,
    email,
    website,
    status,
    notes,
    deleted_at,
    created_at,
    updated_at
)

5. **CLIENT_CONTACTS** (
    **id** [PK],
    #client_id [FK -> CLIENTS.id, ON DELETE CASCADE],
    first_name,
    last_name,
    cin,
    position,
    phone,
    email,
    is_primary,
    created_at,
    updated_at
)

6. **DOSSIERS** (
    **id** [PK],
    reference [UNIQUE],
    #client_id [FK -> CLIENTS.id, ON DELETE CASCADE],
    type,
    description,
    start_date,
    end_date,
    status,
    priority,
    #responsible_id [FK -> EMPLOYEES.id, NULLABLE, ON DELETE SET NULL],
    notes,
    deleted_at,
    created_at,
    updated_at
)

7. **DOSSIER_EMPLOYEES** *(Table de liaison N:N - RG03, RG04)* (
    **id** [PK],
    #dossier_id [FK -> DOSSIERS.id, ON DELETE CASCADE],
    #employee_id [FK -> EMPLOYEES.id, ON DELETE CASCADE],
    role_in_dossier,
    assigned_date,
    created_at,
    updated_at,
    UNIQUE(dossier_id, employee_id)
)

8. **DECLARATION_TYPES** (
    **id** [PK],
    name,
    code [UNIQUE],
    periodicity,
    description,
    created_at,
    updated_at
)

9. **DECLARATIONS** (
    **id** [PK],
    #client_id [FK -> CLIENTS.id, ON DELETE CASCADE],
    #dossier_id [FK -> DOSSIERS.id, NULLABLE, ON DELETE SET NULL],
    #declaration_type_id [FK -> DECLARATION_TYPES.id, ON DELETE RESTRICT],
    period,
    due_date [INDEX],
    filing_date,
    amount,
    status [INDEX],
    filing_reference,
    #responsible_id [FK -> EMPLOYEES.id, NULLABLE, ON DELETE SET NULL],
    comments,
    created_at,
    updated_at
)

10. **INVOICES** (
    **id** [PK],
    reference [UNIQUE],
    #client_id [FK -> CLIENTS.id, ON DELETE RESTRICT],
    invoice_date,
    due_date [INDEX],
    subtotal_ht,
    tax_amount,
    total_ttc,
    paid_amount,
    remaining_amount,
    status [INDEX],
    payment_conditions,
    notes,
    created_at,
    updated_at
)

11. **INVOICE_ITEMS** (
    **id** [PK],
    #invoice_id [FK -> INVOICES.id, ON DELETE CASCADE],
    description,
    quantity,
    unit_price,
    tax_rate,
    total_ht,
    total_ttc,
    created_at,
    updated_at
)

12. **PAYMENTS** (
    **id** [PK],
    #invoice_id [FK -> INVOICES.id, ON DELETE CASCADE],
    #client_id [FK -> CLIENTS.id, ON DELETE RESTRICT],
    amount,
    payment_date,
    payment_method,
    reference,
    bank,
    comments,
    #created_by [FK -> USERS.id, NULLABLE, ON DELETE SET NULL],
    created_at,
    updated_at
)

13. **DOCUMENTS** (
    **id** [PK],
    title,
    file_name,
    file_path,
    file_type,
    file_size,
    category [INDEX],
    #client_id [FK -> CLIENTS.id, NULLABLE, ON DELETE CASCADE],
    #dossier_id [FK -> DOSSIERS.id, NULLABLE, ON DELETE CASCADE],
    #uploaded_by [FK -> USERS.id, NULLABLE, ON DELETE SET NULL],
    notes,
    created_at,
    updated_at
)

14. **DEADLINES** (
    **id** [PK],
    title,
    due_date [INDEX],
    type,
    status,
    priority,
    reminder_days,
    #client_id [FK -> CLIENTS.id, NULLABLE, ON DELETE CASCADE],
    #dossier_id [FK -> DOSSIERS.id, NULLABLE, ON DELETE CASCADE],
    #responsible_id [FK -> EMPLOYEES.id, NULLABLE, ON DELETE SET NULL],
    created_at,
    updated_at
)

15. **ACTIVITY_LOGS** (
    **id** [PK],
    #user_id [FK -> USERS.id, NULLABLE, ON DELETE SET NULL],
    user_name,
    module [INDEX],
    action,
    description,
    target_id,
    target_label,
    ip_address,
    user_agent,
    created_at,
    updated_at
)

16. **NOTIFICATIONS** (
    **id** [PK],
    #user_id [FK -> USERS.id, NULLABLE, ON DELETE CASCADE],
    title,
    message,
    type,
    link,
    is_read [INDEX],
    read_at,
    created_at,
    updated_at
)

---

## 3. Validation des Formes Normales

- **1ère Forme Normale (1NF) :** Tous les attributs sont atomiques (pas de listes multivaluées dans une même cellule, décomposition des adresses et identités).
- **2ème Forme Normale (2NF) :** Les attributs non clés dépendent entièrement de la totalité de la clé primaire (particulièrement respecté sur `dossier_employees` où `role_in_dossier` dépend du couple `(dossier_id, employee_id)`).
- **3ème Forme Normale (3NF) :** Aucune dépendance transitive entre attributs non clés (les données du client ne sont pas dupliquées dans la table `dossiers` ou `invoices`, seule la clé étrangère `client_id` est référencée).
