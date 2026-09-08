# LIVRABLE 2 : MODÈLE CONCEPTUEL DES DONNÉES (MCD - MERISE)

**Projet :** Système de Gestion d’un Cabinet Fiduciaire (FiducialPro)  
**Contexte Réglementaire :** Droit des sociétés et fiscalité marocaine (CGI, CNSS, OEC)  
**Méthode :** MERISE  

---

## 1. Dictionnaire des Données

| Code Donnée | Désignation | Type | Longueur | Contraintes |
| :--- | :--- | :--- | :--- | :--- |
| `id_role` | Identifiant du rôle | Entier | AI | PK, Non nul |
| `nom_role` | Libellé du rôle (Admin, Gérant, Comptable, Secrétaire) | Texte | 50 | Unique |
| `id_user` | Identifiant de l'utilisateur | Entier | AI | PK, Non nul |
| `email_user` | Adresse email de connexion | Texte | 191 | Unique |
| `password_user` | Empreinte chiffrée (Bcrypt) du mot de passe | Texte | 255 | Non nul |
| `id_emp` | Identifiant collaborateur | Entier | AI | PK, Non nul |
| `mat_emp` | Matricule interne du collaborateur | Texte | 30 | Unique |
| `cin_emp` | Carte d'Identité Nationale (CIN) | Texte | 20 | Unique |
| `id_client` | Identifiant du client | Entier | AI | PK, Non nul |
| `ice_client` | Identifiant Commun de l'Entreprise | Chaine | 15 | Unique (15 chiffres) |
| `if_client` | Identifiant Fiscal (DGI) | Chaine | 30 | Indexé |
| `rc_client` | Registre de Commerce (Tribunal de commerce) | Chaine | 30 | Indexé |
| `forme_jur` | Forme juridique (SARL, SA, SARL AU, etc.) | Texte | 50 | Liste fermée |
| `id_dossier` | Identifiant du dossier | Entier | AI | PK, Non nul |
| `ref_dossier` | Référence unique de mission (`DOS-YYYY-XXXX`) | Chaine | 30 | Unique |
| `type_mission` | Nature de prestation (Compta, Fiscal, Juridique...) | Texte | 50 | Énumération |
| `id_type_decl` | Identifiant de l'obligation fiscale | Entier | AI | PK, Non nul |
| `code_decl` | Code taxe (TVA, IS, IR, CNSS, 9421) | Chaine | 20 | Unique |
| `id_decl` | Identifiant de la déclaration | Entier | AI | PK, Non nul |
| `period_decl` | Période fiscale concernée | Texte | 50 | Non nul |
| `echeance_decl` | Date limite de dépôt légal | Date | - | Non nul |
| `ref_simpl` | Référence de télétransmission SIMPL/DGI | Texte | 100 | Nullable |
| `id_facture` | Identifiant de la facture | Entier | AI | PK, Non nul |
| `ref_facture` | Numérotation chronologique (`FAC-YYYY-XXXX`) | Chaine | 30 | Unique |
| `montant_ht` | Sous-total Hors Taxes en Dirhams | Décimal | 12,2 | Non nul, >= 0 |
| `montant_tva` | Montant de la TVA (généralement 20%) | Décimal | 12,2 | Non nul, >= 0 |
| `montant_ttc` | Total Toutes Taxes Comprises | Décimal | 12,2 | Non nul, >= 0 |
| `id_ligne_fac` | Identifiant ligne de facture | Entier | AI | PK, Non nul |
| `id_paiement` | Identifiant du paiement reçu | Entier | AI | PK, Non nul |
| `montant_paye` | Montant du règlement en Dirhams | Décimal | 12,2 | > 0, <= Reste dû |
| `id_document` | Identifiant du document archivé | Entier | AI | PK, Non nul |
| `chemin_doc` | Chemin d'accès sur le système de stockage | Texte | 255 | Non nul |
| `id_echeance` | Identifiant du jalon / deadline | Entier | AI | PK, Non nul |
| `id_log` | Identifiant trace d'audit | Entier | AI | PK, Non nul |

---

## 2. Description des Entités & Attributs

### `ROLE`
- **Identifiant :** `id`
- **Attributs :** `name`, `slug`, `description`
- *Remarque :* le `slug` (`admin`, `gerant`, `comptable`, `secretaire`) constitue la clé du contrôle d'accès ; la matrice des droits est implémentée dans les Policies applicatives (§4 du livrable 7) et non dans une table de permissions.

### `USER`
- **Identifiant :** `id`
- **Attributs :** `name`, `email`, `password`, `phone`, `status`, `last_login_at`

### `EMPLOYEE`
- **Identifiant :** `id`
- **Attributs :** `matricule`, `cin`, `first_name`, `last_name`, `email`, `phone`, `address`, `position`, `hire_date`, `salary`, `status`, `notes`

### `CLIENT`
- **Identifiant :** `id`
- **Attributs :** `type` (entreprise/particulier), `company_name`, `trade_name`, `legal_form`, `ice`, `if_number`, `rc_number`, `patent_number`, `cnss_number`, `share_capital`, `activity`, `address`, `city`, `phone`, `email`, `status`, `notes`

### `CLIENT_CONTACT`
- **Identifiant :** `id`
- **Attributs :** `first_name`, `last_name`, `cin`, `position`, `phone`, `email`, `is_primary`

### `DOSSIER`
- **Identifiant :** `id`
- **Attributs :** `reference` (`DOS-YYYY-XXXX`), `type`, `description`, `start_date`, `end_date`, `status`, `priority`, `notes`

### `DECLARATION_TYPE`
- **Identifiant :** `id`
- **Attributs :** `name`, `code`, `periodicity`, `description`

### `DECLARATION`
- **Identifiant :** `id`
- **Attributs :** `period`, `due_date`, `filing_date`, `amount`, `status`, `filing_reference`, `comments`

### `INVOICE`
- **Identifiant :** `id`
- **Attributs :** `reference` (`FAC-YYYY-XXXX`), `invoice_date`, `due_date`, `subtotal_ht`, `tax_amount`, `total_ttc`, `paid_amount`, `remaining_amount`, `status`, `payment_conditions`, `notes`

### `INVOICE_ITEM`
- **Identifiant :** `id`
- **Attributs :** `description`, `quantity`, `unit_price`, `tax_rate`, `total_ht`, `total_ttc`

### `PAYMENT`
- **Identifiant :** `id`
- **Attributs :** `amount`, `payment_date`, `payment_method`, `reference`, `bank`, `comments`

### `DOCUMENT`
- **Identifiant :** `id`
- **Attributs :** `title`, `file_name`, `file_path`, `file_type`, `file_size`, `category`, `notes`

### `DEADLINE`
- **Identifiant :** `id`
- **Attributs :** `title`, `due_date`, `type`, `status`, `priority`, `reminder_days`

### `ACTIVITY_LOG`
- **Identifiant :** `id`
- **Attributs :** `module`, `action`, `description`, `target_id`, `target_label`, `ip_address`, `user_agent`

---

## 3. Relations & Cardinalités MERISE

```
[ROLE] (1,1) ------- Posséder ------- (0,N) [USER]
[USER] (0,1) ------- Associer ------- (0,1) [EMPLOYEE]

[CLIENT] (1,1) ----- Rapprocher ----- (0,N) [CLIENT_CONTACT]
[CLIENT] (1,1) ----- Ouvrir --------- (0,N) [DOSSIER]
[CLIENT] (1,1) ----- Concerner ------ (0,N) [DECLARATION]
[CLIENT] (1,1) ----- Facturer ------- (0,N) [INVOICE]
[CLIENT] (0,1) ----- Rattacher ------ (0,N) [DOCUMENT]

[DOSSIER] (1,1) ---- Piloter -------- (0,N) [EMPLOYEE] (Responsable superviseur)
[DOSSIER] (0,N) ==== AFFECTER ======= (0,N) [EMPLOYEE] (Collaborateurs exécutants)
                     Attributs de relation : role_in_dossier, assigned_date

[DOSSIER] (0,1) ---- Segmenter ------ (0,N) [DECLARATION]
[DOSSIER] (0,1) ---- Rattacher ------ (0,N) [DOCUMENT]

[DECLARATION_TYPE] (1,1) -- Typer --- (0,N) [DECLARATION]
[EMPLOYEE] (0,1) --- Instruire ------ (0,N) [DECLARATION]

[INVOICE] (1,1) ---- Composer ------- (1,N) [INVOICE_ITEM]
[INVOICE] (1,1) ---- Régler --------- (0,N) [PAYMENT]

[USER] (0,1) ------- Déposer -------- (0,N) [DOCUMENT]
[USER] (0,1) ------- Enregistrer ---- (0,N) [PAYMENT]
[USER] (0,1) ------- Tracer --------- (0,N) [ACTIVITY_LOG]
```

### Justification des Cardinalités :
1. **CLIENT <-> DOSSIER (1,1 / 0,N) :** Un dossier appartient obligatoirement à 1 et 1 seul client. Un client peut avoir 0 ou plusieurs dossiers (missions comptables, juridiques, fiscales).
2. **DOSSIER <-> EMPLOYEE (N:N via AFFECTER) :** Conformément à l'exigence **RG03 et RG04**, un dossier peut mobiliser plusieurs collaborateurs (comptable de saisie, superviseur, juriste) et un collaborateur gère simultanément un portefeuille de plusieurs dossiers. La table d'association porte les propriétés `role_in_dossier` et `assigned_date`.
3. **INVOICE <-> INVOICE_ITEM (1,1 / 1,N) :** Une facture est obligatoirement décomposée en une ou plusieurs lignes de prestations (honoraires mensuels, débours, déclarations).
4. **INVOICE <-> PAYMENT (1,1 / 0,N) :** Une facture peut faire l'objet de paiements échelonnés (acomptes, règlements partiels). Chaque versement est rattaché à sa facture avec contrôle strict **RG08** : la somme des versements ne peut excéder le total TTC.
5. **DECLARATION_TYPE <-> DECLARATION (1,1 / 0,N) :** Chaque déclaration dépend d'une obligation fiscale légale paramétrée (TVA, Acompte IS, IR, Bordereau CNSS).
