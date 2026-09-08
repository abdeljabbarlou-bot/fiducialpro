# FiducialPro - Système Intégré de Gestion de Cabinet Fiduciaire

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38bdf8.svg)](https://tailwindcss.com)
[![Tests](https://img.shields.io/badge/PHPUnit-11%20Passed-brightgreen.svg)]()
[![License](https://img.shields.io/badge/Licence-Propri%C3%A9taire-slate.svg)]()

> Application web professionnelle complète conçue pour la gestion opérationnelle, fiscale, comptable et financière d'un cabinet fiduciaire et d'expertise comptable, développée dans le respect des normes réglementaires et fiscales marocaines (CGI, DGI SIMPL, CNSS, Ordre des Experts Comptables).

---

## 🌟 Fonctionnalités & Modules Métier

1. **Tableau de Bord Exécutif & Analytique :**
   - 4 Indicateurs financiers et opérationnels en temps réel (CA HT/TTC, encaissements, créances clients, alertes de retard).
   - 4 Graphiques interactifs alimentés par **Chart.js** (Évolution mensuelle du CA sur 6 mois, répartition des dossiers par nature de mission, statut du portefeuille de facturation, typologie des obligations fiscales).
   - Fil d'actualité des déclarations urgentes et flux des dernières opérations de l'équipe.

2. **Gestion du Portefeuille Clients :**
   - Fiche d'identification entreprise complète : Raison sociale, enseigne, forme juridique (SARL, SARL AU, SA, SNC, Auto-entrepreneur, Personne physique).
   - Identifiants légaux marocains stricts : **ICE** (15 chiffres), **Identifiant Fiscal (IF)**, **Registre du Commerce (RC)**, **Taxe Professionnelle / Patente**, **Numéro CNSS**, Capital social.
   - Carnet des représentants légaux et dirigeants.
   - Suppression logique (**Soft Delete**) avec possibilité d'archivage et restauration.

3. **Gestion des Dossiers & Missions :**
   - Référence séquentielle normalisée générée automatiquement : `DOS-YYYY-XXXX`.
   - Typologie de prestations : Tenue comptable annuelle, Fiscalité & TVA, Conseil de gestion, Paie & RH, Secrétariat juridique, Constitution de société.
   - Niveaux de priorité (Faible, Moyenne, Haute, Urgente) et suivi d'avancement par jalons.

4. **Affectation Collaborative des Équipes (N:N - RG03, RG04) :**
   - Affectation dynamique de plusieurs collaborateurs sur un même dossier avec désignation d'un superviseur référent et rôles individualisés (Saisie, Rapprochement, Audit).
   - Suivi précis de la charge de travail individuelle de chaque membre du cabinet.

5. **Déclarations Fiscales & Télédéclaration SIMPL (RG09, RG10, RG11) :**
   - Prise en charge des obligations fiscales : **TVA** (Trimestrielle et Mensuelle), **Acomptes IS**, **Solde IS**, **IR Salarial**, **Bordereaux CNSS**, **État 9421**.
   - **Moteur d'alerte automatique de retard (RG11) :** Détection synchrone des déclarations non déposées dont la date limite légale est échue avec signalement visuel clignotant.
   - Validation de télétransmission avec enregistrement de la date effective et du numéro de récépissé électronique délivré par la DGI (SIMPL / DAMANCOM).

6. **Facturation des Honoraires (RG06, RG07) :**
   - Numérotation chronologique continue inviolable : `FAC-YYYY-XXXX`.
   - Éditeur de lignes dynamique sous **Alpine.js** : Ajout/suppression de lignes, calcul automatique en temps réel des montants HT, TVA (20%, 14%, 10%, 7%, 0%) et TTC.
   - Génération instantanée de **Factures officielles en PDF** (A4 prêt pour impression avec en-tête cabinet, identifiants du client et mentions légales) via `barryvdh/laravel-dompdf`.
   - Fonction d'impression directe optimisée pour les navigateurs web.

7. **Encaissements & Règlements Sécurisés (RG07, RG08) :**
   - Traçabilité complète des règlements (Virement bancaire, chèque, espèces, carte bancaire).
   - **Contrôle strict RG08 :** Le système bloque formellement tout versement supérieur au solde restant dû de la facture.
   - Synchronisation automatique en temps réel du statut de la facture (`emise` -> `partiellement_payee` -> `payee`).
   - Génération de **Reçus de Paiement Officiels** acquittés prêts à imprimer pour remise aux clients.

8. **Gestion Électronique des Documents (GED - RG12) :**
   - Stockage et archivage sécurisé des pièces comptables, statuts notariés, modèles J, procès-verbaux d'assemblée générale et bilans.
   - Contrôle d'intégrité : Vérification des types MIME autorisés (PDF, DOCX, XLSX, JPG, PNG) et limitation stricte à 10 Mo par document.
   - Rattachement obligatoire à une entreprise cliente ou à un dossier de mission.

9. **Calendrier Mensuel des Échéances Légales :**
   - Vue bivalente : Grille mensuelle de calendrier interactif et liste tabulaire filtrable.
   - Marquage interactif des jalons terminés et rappels proactifs.

10. **Rapports & Exports Analytiques (RG16) :**
    - 4 Modules d'exportation avec filtres par date et critères avancés : Financier & Facturation, Portefeuille Clients, Déclarations Fiscales, Dossiers & Missions.
    - Double format de sortie : **Classeurs Excel natifs (.xlsx)** au format professionnel (en-tête aux couleurs du cabinet, colonnes formatées, ligne de totaux — généré via PhpSpreadsheet) et **Rapports de synthèse en PDF**.

11. **Contrôle d'Accès Basé sur les Rôles (RBAC - RG15) :**
    - 4 Rôles système intégrés : **Administrateur**, **Gérant**, **Comptable**, **Secrétaire**.
    - Middleware applicatif `CheckRole` (accès aux modules) combiné à des **Policies Laravel** dédiées (`ClientPolicy`, `DossierPolicy`, `EmployeePolicy`, `DeclarationPolicy`, `DocumentPolicy`) qui appliquent au niveau de chaque action (création/modification/suppression/télé-dépôt) la matrice de droits exacte définie au cahier des charges — l'interface masque également les actions non autorisées.

12. **Corbeille & Restauration (RG02) :**
    - Toute fiche supprimée (client, dossier, collaborateur) part en corbeille au lieu d'être effacée : l'historique comptable reste intact et restaurable en un clic par l'Administrateur ou le Gérant.
    - **Garde-fou d'intégrité :** un client rattaché à des factures, règlements ou déclarations ne peut pas être supprimé (l'archivage est imposé), et une facture conserve toujours l'identité de son client même si celui-ci est en corbeille.

13. **Sécurité & Journal d'Audit Intégral (RG14) :**
    - Journalisation automatique et immuable de chaque action : création, modification, paiement, suppression, téléversement de fichier et connexion, incluant l'adresse IP et l'empreinte navigateur.
    - Recherche transversale globale instantanée sur l'ensemble des modules du système.

---

## 🚀 Installation Rapide

### Prérequis Système
- **PHP** : Version 8.2 ou supérieure avec les extensions suivantes activées :
  `zip`, `fileinfo`, `sqlite3` ou `pdo_mysql`, `mbstring`, `openssl`, `dom`, `xml`, `simplexml`, `iconv`, `ctype`, `filter`
  et **`gd`** (indispensable à la génération des classeurs Excel via PhpSpreadsheet).

  > ⚠️ **Extension `gd`** — elle est désactivée par défaut sous XAMPP/WAMP et `composer install`
  > échouera sans elle. Pour l'activer : ouvrir `php.ini` (chemin donné par `php --ini`),
  > retirer le `;` devant la ligne `;extension=gd`, enregistrer, puis vérifier avec :
  > ```bash
  > php -m | findstr gd
  > ```
- **Composer** : Version 2.x
- **Node.js** : Version 18 ou supérieure avec npm (compilation des assets front-end via Vite).
- **Serveur Web** : Apache / Nginx ou serveur de développement local Artisan.

### Étapes d'installation

```bash
# 1. Cloner ou ouvrir le projet dans votre terminal
cd c:\Users\HP\Desktop\projetStage

# 2. Installer les dépendances PHP
composer install

# 3. Configurer le fichier d'environnement
cp .env.example .env
php artisan key:generate

# 4. Créer le lien symbolique pour le stockage GED
php artisan storage:link

# 5. Exécuter les migrations et charger le jeu de données de démonstration complet
php artisan migrate:fresh --seed

# 6. Compiler les assets front-end (Tailwind, Alpine.js, Chart.js, Font Awesome)
npm install
npm run build

# 7. Démarrer le serveur d'application local
php artisan serve
```

> 💡 **Étape 6 obligatoire.** L'interface (styles, icônes, graphiques, modales) est compilée
> localement par Vite : l'application fonctionne intégralement **sans connexion Internet**.
> Sans cette étape, aucune feuille de style ne sera chargée.
> En développement, `npm run dev` active le rechargement à chaud.

L'application est immédiatement accessible à l'adresse : **`http://127.0.0.1:8000`**

### ⚙️ Paramétrage de l'identité du cabinet

Les mentions légales figurant sur les factures, les reçus de règlement et les rapports
(raison sociale, adresse, ICE, IF, RC, patente, coordonnées bancaires) sont centralisées
dans **`config/cabinet.php`** et pilotées par les variables `CABINET_*` du fichier `.env`.
Un changement d'adresse ou de RIB ne nécessite donc qu'une seule modification :

```dotenv
CABINET_NAME="FiducialPro"
CABINET_ADDRESS="120 Boulevard d'Anfa, 5ème étage"
CABINET_ICE="001548792000034"
CABINET_BANK_RIB="007 780 0001234567890123 45"
```

---

## 🔑 Identifiants d'Accès par Défaut (Démonstration)

Le mot de passe par défaut pour tous les comptes est : **`password`**

| Profil / Rôle | Adresse Email | Droits & Attributions |
| :--- | :--- | :--- |
| **Administrateur Système** | `admin@cabinet.ma` | Accès complet, gestion des utilisateurs, journal d'audit de sécurité. |
| **Gérant Associé** | `gerant@cabinet.ma` | Pilotage stratégique, tableaux de bord financiers, validation des dossiers. |
| **Comptable Senior** | `comptable1@cabinet.ma` | Gestion des dossiers, déclarations TVA/IS, facturation et paiements. |
| **Comptable Junior** | `comptable2@cabinet.ma` | Saisie comptable, télétransmission SIMPL et gestion des pièces. |
| **Secrétaire** | `secretaire@cabinet.ma` | Accueil clients, création de fiches, archivage GED et prise de rendez-vous. |

> **Astuce de test :** Sur la page de connexion (`/login`), des boutons d'accès rapide sont disponibles pour basculer instantanément d'un profil à un autre en un seul clic !

---

## 🧪 Tests Automatisés & Validation Qualité

Le projet inclut une suite de tests unitaires et fonctionnels complets validant la conformité rigoureuse des règles de gestion métier **RG01 à RG16** (calcul arithmétique de la TVA, rejet des sur-paiements RG08, transition d'état des factures, détection automatique des retards fiscaux RG11, et restrictions de sécurité RBAC).

Pour exécuter les tests :

```bash
php artisan test
```

Résultat d'exécution validé :
```text
PASS  Tests\Feature\ExampleTest
✓ guest is redirected to login
✓ login page is accessible

PASS  Tests\Feature\FiduciaireBusinessRulesTest
✓ client creation and ice format
✓ dossier employee assignment
✓ invoice totals calculation
✓ payment cannot exceed remaining amount
✓ payment updates invoice status to paid
✓ declaration overdue sync
✓ rbac secretaire cannot access user management
✓ rbac admin can access user management
✓ client ice validation rules
✓ user create and edit views render
✓ user deletion and self prevention
✓ rbac secretaire denied financial operations
✓ notifications and reports pages
✓ rbac comptable cannot create or delete client
✓ rbac secretaire cannot update or delete client
✓ rbac secretaire dossiers consultation only
✓ rbac comptable cannot manage employees
✓ invoice rejects illegal tva rate
✓ payment date must be coherent
✓ login is rate limited after failed attempts
✓ deleted client can be restored from trash
✓ trash is restricted to admin and gerant
✓ rbac secretaire declarations consultation only
✓ declaration filing date cannot be in the future
✓ rbac document deletion restricted to direction
✓ admin cannot lock himself out
✓ last active admin cannot be removed
✓ client with accounting history cannot be deleted
✓ invoice keeps client identity when client is trashed
✓ deleted employee can be restored and keeps his assignments

Tests:    32 passed (115 assertions)
Duration: ~30s
```

---

## 📁 Livrables du Projet

Tous les livrables d'ingénierie logicielle et de conception sont disponibles dans le répertoire `docs/` :
- `docs/LIVRABLE_2_MCD.md` : Modèle Conceptuel des Données (Méthode MERISE) avec dictionnaire de données et cardinalités.
- `docs/LIVRABLE_3_MLD.md` : Modèle Logique des Données normalisé en 3ème Forme Normale (3NF).
- `docs/LIVRABLE_4_MPD.sql` : Script SQL DDL complet et exécutable de création de la base de données relationnelle.
- `docs/LIVRABLE_7_DOCUMENTATION_TECHNIQUE.md` : Documentation technique détaillée, architecture logicielle, matrice RBAC et diagrammes de séquence.
- `docs/LIVRABLE_8_GUIDE_UTILISATEUR.md` : Manuel d'utilisation opérationnel pas à pas structuré par profil métier.
- `docs/LIVRABLE_11_SUPPORT_SOUTENANCE.md` : Support de présentation et trame complète pour la soutenance de stage / PFE.
