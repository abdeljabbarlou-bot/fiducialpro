# LIVRABLE 11 : SUPPORT DE SOUTENANCE DE STAGE / PROJET DE FIN D'ÉTUDES (PFE)

**Sujet :** Conception et Réalisation d'une Plateforme Web Intégrée de Gestion d'un Cabinet Fiduciaire et d'Expertise Comptable (**FiducialPro**)  
**Contexte :** Droit des Sociétés et Fiscalité Marocaine  
**Format :** Trame de diapositives de soutenance orale (Présentation 20-30 minutes)  

---

## 🖥️ SLIDE 1 : Titre & Introduction
### Système Intégré de Gestion pour Cabinet Fiduciaire
**Projet de Fin d'Études / Stage Professionnel**

- **Candidat :** [Votre Nom & Prénom]
- **Filière :** Ingénierie Logicielle / Informatique de Gestion
- **Encadrant Pédagogique :** [Nom du Professeur]
- **Encadrant Professionnel :** [Nom de l'Expert-Comptable / Maître de Stage]
- **Période :** Année Académique 2025 - 2026
- **Établissement :** [Nom de l'École / Université]

---

## 🖥️ SLIDE 2 : Contexte & Problématique Métier
### Le Défi Quotidien d'un Cabinet Fiduciaire au Maroc

- **Multiplicité des obligations légales :**
  * Suivi rigoureux des déclarations fiscales (TVA mensuelle/trimestrielle, acomptes IS, solde IS, IR).
  * Risques de pénalités et majorations fiscales élevées en cas de retard de dépôt.
- **Fragmentation de l'information :**
  * Données clients dispersées entre classeurs physiques, courriels et feuilles Excel multiples.
  * Absence de traçabilité des pièces comptables et justificatifs administratifs.
- **Enjeux de facturation et de trésorerie :**
  * Difficulté à suivre les honoraires émis, les relances clients et les encaissements partiels.
  * Risque de saisie erronée sur les soldes restants dus sans contrôle automatique strict.
- **Nécessité d'une solution unifiée :**
  * Centraliser la gestion des dossiers, des collaborateurs, de la facturation et de la conformité SIMPL dans une interface moderne et sécurisée.

---

## 🖥️ SLIDE 3 : Objectifs du Projet & Périmètre
### Une Plateforme All-in-One Dédiée aux Professionnels du Chiffre

- **Objectifs Opérationnels :**
  1. Centraliser 100% des clients, dossiers et interlocuteurs juridiques.
  2. Automatiser la détection des échéances et des retards fiscaux (**Règle RG11**).
  3. Sécuriser la facturation des honoraires et les encaissements (**Règle RG08**).
  4. Mettre en place un espace documentaire numérique certifié (GED - **RG12**).
  5. Fournir aux dirigeants une visibilité financière en temps réel par des indicateurs clés (KPIs).

- **14 Modules Implémentés :**
  * Authentification & Contrôle d'Accès RBAC
  * Gestion du Portefeuille Clients (ICE, IF, RC, CNSS)
  * Missions & Dossiers Clients
  * Affectation Collaborative des Équipes (N:N)
  * Déclarations Fiscales & Télédéclarations SIMPL
  * Facturation d'Honoraires & Édition PDF
  * Encaissements & Reçus de Caisse
  * Gestion Électronique des Documents (GED)
  * Calendrier & Échéancier Mensuel
  * Tableau de Bord Décisionnel & Graphiques Chart.js
  * Rapports d'Activité & Exports Excel (.xlsx) / PDF
  * Gestion des Collaborateurs & Personnel
  * Recherche Transversale Globale
  * Journal d'Audit & Sécurité

---

## 🖥️ SLIDE 4 : Conception & Modélisation (Méthode MERISE)
### Structuration Rigoureuse du Système d'Information

- **Dictionnaire des Données :** Plus de 50 propriétés métier typées et indexées.
- **Modèle Conceptuel des Données (MCD) :**
  * Modélisation des entités principales : `CLIENT`, `DOSSIER`, `EMPLOYEE`, `INVOICE`, `PAYMENT`, `DECLARATION`, `DOCUMENT`.
  * Prise en compte de la relation complexe N:N entre `DOSSIER` et `EMPLOYEE` avec portage des attributs de relation `role_in_dossier` et `assigned_date`.
- **Modèle Logique des Données (MLD) :**
  * Normalisation intégrale en 3ème Forme Normale (**3NF**).
  * Définition précise des clés primaires, clés étrangères et politiques d'intégrité (`CASCADE` pour les lignes de factures, `SET NULL` pour les responsables, `RESTRICT` pour les clients facturés).
- **Modèle Physique des Données (MPD) :**
  * Script SQL DDL complet de 16 tables avec index d'optimisation sur les champs de recherche fréquente (`ice`, `status`, `due_date`).

---

## 🖥️ SLIDE 5 : Architecture Technique & Pile Technologique
### Un Socle Moderne, Robuste et Évolutif

- **Backend :**
  * **Laravel 12 (PHP 8.2+) :** Architecture MVC élégante, ORM Eloquent, migrations, seeders, middleware de sécurité.
  * **Service Layer Pattern :** Découplage de la logique métier critique (`PaymentService`, `InvoiceService`, `DeclarationService`, `DocumentService`).
- **Frontend & Expérience Utilisateur :**
  * **Blade Templating Engine :** Structure modulaire et composants réutilisables.
  * **Tailwind CSS :** Interface utilisateur moderne, ergonomique et responsive adaptée aux exigences bureautiques.
  * **Alpine.js :** Réactivité frontend dynamique sans rechargement de page (lignes dynamiques de calcul de facture, onglets, fenêtres modales).
- **Visualisation & Restitution :**
  * **Chart.js :** 4 graphiques interactifs pour l'aide à la décision.
  * **DomPDF :** Génération native de documents PDF A4 haute fidélité (Factures d'honoraires, synthèses périodiques).
- **Qualité & Tests :**
  * **PHPUnit :** 11 tests automatisés validant la conformité des règles métier.

---

## 🖥️ SLIDE 6 : Règles de Gestion Métier Clés (RG01 à RG16)
### L'Intelligence Métier au Coeur du Système

- **Règle RG06 & RG07 (Arithmétique Financière & Facturation) :**
  * Numérotation continue `FAC-YYYY-XXXX`.
  * Calcul instantané ligne par ligne : $\text{HT} = \text{Qté} \times \text{PU}$, $\text{TVA} = \text{HT} \times 20\%$, $\text{TTC} = \text{HT} + \text{TVA}$.
- **Règle RG08 (Contrôle Strict de l'Encaissement) :**
  * Interdiction absolue d'encaisser un montant supérieur au solde restant dû :
    $$\text{Montant Saisi} \le \text{Solde Restant Dû}$$
  * Transition automatique de statut : `émise` $\to$ `partiellement payée` $\to$ `payée`.
- **Règle RG11 (Détection Proactive des Retards Fiscaux) :**
  * Synchronisation en arrière-plan à chaque requête :
    $$\text{Si } (\text{Statut} \ne \text{'déposée'} \text{ ET } \text{Date Limite} < \text{Aujourd'hui}) \implies \text{Statut} = \text{'en retard'}$$
- **Règle RG14 (Audit Trail) :**
  * Journalisation automatique de chaque transaction avec opérateur, adresse IP et empreinte horodatée.

---

## 🖥️ SLIDE 7 : Démonstration de l'Application
### Parcours des Fonctionnalités Majeures

1. **Dashboard & Pilotage Exécutif :**
   * Visualisation du chiffre d'affaires, des encaissements et des alertes de retard immédiates.
2. **Gestion Client 360° :**
   * Consultation d'un client avec ses 6 onglets intégrés : Dossiers, Déclarations, Factures, GED, Fiche Juridique, Audit log.
3. **Module Dossiers & Affectation Collaborateurs (N:N) :**
   * Attribution de missions et répartition d'équipe.
4. **Facturation & Règlements :**
   * Saisie dynamique avec Alpine.js, génération de la facture PDF et enregistrement d'un règlement avec génération du reçu officiel.
5. **Déclarations Fiscales :**
   * Validation de télétransmission avec saisie du récépissé SIMPL de la DGI.

---

## 🖥️ SLIDE 8 : Sécurité & Contrôle d'Accès (RBAC)
### Protection des Données Confidentielles du Cabinet

- **Contrôle d'Accès par Rôles (RBAC) :**
  * **Administrateur :** Paramétrage système, création d'utilisateurs, consultation du journal d'audit complet.
  * **Gérant :** Pilotage d'activité, consultation financière globale et validation.
  * **Chef de Mission / Comptable :** Traitement des dossiers, déclarations et factures.
  * **Secrétaire :** Accueil client, enregistrement de coordonnées et archivage de pièces.
- **Sécurité Applicative :**
  * Mots de passe chiffrés avec l'algorithme `Bcrypt`.
  * Protection native contre les failles courantes : CSRF (jetons de formulaire), Injections SQL (requêtes préparées PDO Eloquent), XSS (échappement automatique Blade).

---

## 🖥️ SLIDE 9 : Validation & Tests Automatisés
### Fiabilité et Conformité Vérifiées

- **Résultats de la suite de tests PHPUnit :**
  * `test_client_creation_and_ice_format` : Validé.
  * `test_dossier_employee_assignment` : Validé.
  * `test_invoice_totals_calculation` : Validé.
  * `test_payment_cannot_exceed_remaining_amount` (RG08) : Validé.
  * `test_payment_updates_invoice_status_to_paid` : Validé.
  * `test_declaration_overdue_sync` (RG11) : Validé.
  * `test_rbac_access_restrictions` : Validé.
- **Bilan Qualité :** **11 tests réussis, 24 assertions, 0 échec.**

---

## 🖥️ SLIDE 10 : Bilan & Perspectives d'Évolution
### Apports du Projet et Évolutions Futures

- **Compétences Techniques & Métier Développées :**
  * Maîtrise avancée du framework Laravel 12 et de l'architecture par couches (Services).
  * Modélisation de bases de données relationnelles complexes en environnement réglementé.
  * Approfondissement des règles fiscales marocaines (TVA, IS, IR, simplifications DGI).
- **Perspectives d'Évolution (Feuille de Route v2.0) :**
  1. **Intégration API Directe SIMPL / DGI :** Télétransmission dématérialisée automatisée via webservices gouvernementaux.
  2. **Rapprochement Bancaire par IA :** Lecture optique (OCR) des relevés de comptes et lettrage automatique des factures impayées.
  3. **Portail Client Dédié :** Accès sécurisé permettant aux clients du cabinet de consulter leurs bilans et télécharger leurs déclarations en toute autonomie.

---

## 🖥️ SLIDE 11 : Conclusion & Remerciements
### Merci pour votre attention !

*Je tiens à remercier chaleureusement les membres du jury, mon encadrant pédagogique ainsi que l'ensemble de l'équipe du cabinet pour leur précieux accompagnement tout au long de ce projet.*

**La parole est à vous pour la session de Questions / Réponses.**
