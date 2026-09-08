# LIVRABLE 1 : CAHIER DES CHARGES FONCTIONNEL ET TECHNIQUE

**Projet :** FiducialPro – Système Intégré de Gestion d’un Cabinet Fiduciaire  
**Version :** 1.0.0 (Soutenance de Fin d'Études / Stage Professionnel)  
**Contexte Juridique et Réglementaire :** Maroc (Code Général des Impôts, Loi sur les Sociétés, Télédéclarations SIMPL DGI & DAMANCOM CNSS)  

---

## 1. Contexte & Problématique Métier

Un cabinet fiduciaire et d'expertise comptable marocain gère au quotidien un volume considérable de données sensibles et d'obligations légales récurrentes :
- **Identification des personnes morales et physiques :** Raison sociale, forme juridique (SARL, SARL AU, SA, SNC, Auto-entrepreneur), identifiants officiels obligatoires (**ICE à 15 chiffres**, Identifiant Fiscal, Registre de Commerce, Patente, Numéro d'affiliation CNSS).
- **Missions et dossiers clients :** Tenue et surveillance comptable, déclarations fiscales, conseil juridique, paie et constitution de sociétés.
- **Conformité fiscale stricte :** Échéances impératives pour les télédéclarations SIMPL (TVA mensuelle/trimestrielle, acomptes et solde de l'Impôt sur les Sociétés, IR salarial, État 9421) assorties de pénalités et majorations de retard substantielles en cas de dépassement.
- **Gestion financière et trésorerie :** Émission chronologique des factures d'honoraires, calculs de TVA légaux (20%), suivi des créances et encaissements partiels ou totaux avec contrôle d'intégrité strict (interdiction du sur-paiement).
- **Gestion Électronique des Documents (GED) :** Centralisation, archivage numérique sécurisé et traçabilité des pièces justificatives (statuts, PV, modèles J, bilans, relevés bancaires).

L'absence d'un outil intégré conduit fréquemment à la dispersion des pièces, aux risques d'omissions d'échéances fiscales et à une perte de visibilité financière globale pour les associés.

---

## 2. Objectifs du Projet

1. **Centralisation complète :** Fédérer l'ensemble des informations clients, dossiers, collaborateurs et documents dans une plateforme unique et collaborative.
2. **Conformité marocaine rigoureuse :** Respecter les normes comptables marocaines, la structure de l'ICE à 15 chiffres, et les flux SIMPL.
3. **Automatisation de la veille fiscale :** Détecter de façon proactive les retards de déclaration (Règle RG11).
4. **Sécurisation financière :** Bloquer tout règlement supérieur au solde restant dû (Règle RG08) et assurer la mise à jour synchrone des états de facturation.
5. **Aide à la décision :** Offrir un tableau de bord exécutif en temps réel alimenté par des graphiques interactifs (Chart.js) et un centre d'exportation de données (PDF / Excel).

---

## 3. Acteurs et Rôles Applicatifs (RBAC)

Le système implémente 4 profils utilisateurs distincts :

1. **Administrateur (`admin`) :**
   - Paramétrage général du système et gestion des comptes utilisateurs.
   - Consultation intégrale du journal d'audit et des logs de traçabilité.
   - Accès sans restriction à l'ensemble des modules opérationnels.
2. **Gérant / Associé (`gerant`) :**
   - Pilotage stratégique, analyse financière (CA, taux de recouvrement, créances).
   - Validation des dossiers majeurs et consultation du journal d'audit.
   - Génération des rapports périodiques de synthèse.
3. **Chef de Mission / Collaborateur Comptable (`comptable`) :**
   - Création et suivi des dossiers de mission.
   - Saisie et validation des télédéclarations fiscales SIMPL.
   - Émission des factures d'honoraires et enregistrement des règlements clients.
   - Dépôt et consultation des pièces GED.
4. **Secrétaire / Accueil Administratif (`secretaire`) :**
   - Création de la fiche d'une nouvelle entreprise cliente lors de son accueil au cabinet.
   - Dépôt initial des pièces justificatives numérisées dans la GED.
   - Prise de rendez-vous et planification sur le calendrier des échéances.
   - Consultation des dossiers de mission (sans droit de création ni de modification).
   - *Restrictions strictes :* Une fois une fiche client créée, sa modification (identifiants fiscaux, forme juridique...) est réservée à l'équipe comptable, au Gérant et à l'Administrateur — la Secrétaire signale toute correction à effectuer. Interdiction formelle d'accès à l'administration des utilisateurs, au journal d'audit, à la gestion des collaborateurs et aux opérations sensibles d'encaissement financier.

---

## 4. Périmètre Fonctionnel (14 Modules Métier)

1. **Module 1 : Authentification & Sécurité RBAC**
   - Connexion sécurisée avec mot de passe chiffré (`Bcrypt`), gestion de session et contrôle d'accès backend via middleware `CheckRole`.
2. **Module 2 : Tableau de Bord Décisionnel (Dashboard)**
   - 4 Cartes d'indicateurs financiers en temps réel (CA facturé, encaissements, solde impayé, alertes retards).
   - 4 Graphiques interactifs (Chart.js) : CA mensuel sur 6 mois, répartition des missions, statut des factures et déclarations.
3. **Module 3 : Gestion du Portefeuille Clients**
   - Fiche complète avec identifiants marocains (**ICE 15 chiffres**, IF, RC, CNSS, Patente).
   - Vue détaillée à 6 onglets dynamiques (Dossiers, Déclarations, Factures, GED, Mentions légales, Audit log).
   - Suppression logique (`SoftDeletes`) pour préserver l'historique comptable.
4. **Module 4 : Dossiers & Missions de Cabinet**
   - Référence séquentielle normalisée `DOS-YYYY-XXXX`.
   - Typologie complète : Comptabilité, Fiscalité, Conseil, Formation, Social/Paie, Juridique, Constitution de société.
   - Suivi par jalons d'avancement et degrés d'urgence.
5. **Module 5 : Affectation Collaborative des Équipes (N:N)**
   - Association de plusieurs collaborateurs par dossier avec rôle spécifique et date d'affectation.
   - Désignation d'un superviseur référent (`responsible_id`).
6. **Module 6 : Déclarations Fiscales & Télédéclarations SIMPL**
   - Gestion des obligations légales : TVA (mensuelle/trimestrielle), acomptes IS, solde IS, IR salarial, CNSS, État 9421.
   - Enregistrement des preuves de dépôt : date effective et référence du récépissé électronique délivré par la DGI.
   - Moteur automatique de bascule au statut `en_retard` (RG11).
7. **Module 7 : Facturation des Honoraires**
   - Numérotation chronologique continue `FAC-YYYY-XXXX`.
   - Lignes de prestations dynamiques sous Alpine.js avec calcul automatique HT/TVA/TTC côté serveur.
   - Édition de factures officielles au format PDF A4 (DomPDF) et impression navigateur directe.
8. **Module 8 : Suivi des Règlements & Encaissements**
   - Traçabilité des règlements (virement, chèque, espèces, carte bancaire).
   - Contrôle strict anti-surpaiement (RG08) avec verrouillage transactionnel.
   - Génération de reçus d'encaissement acquittés prêts à imprimer.
9. **Module 9 : Gestion Électronique des Documents (GED)**
   - Dépôt sécurisé avec contrôle strict des formats (`pdf, docx, xlsx, jpg, jpeg, png`) — vérifiés à la fois sur l'extension déclarée et sur la signature binaire réelle du fichier — et de la taille (max 10 Mo).
   - Stockage sur disque privé `local` avec téléchargement protégé et contrôlé.
   - Rattachement obligatoire à un client ou à un dossier de mission (RG12).
10. **Module 10 : Calendrier & Échéancier Mensuel**
    - Vue mensuelle interactive et vue liste avec alertes d'imminence et de retard.
11. **Module 11 : Gestion des Collaborateurs RH**
    - Fiche collaborateur (matricule unique, CIN unique, poste, salaire, statut).
    - Provisionnement optionnel et conjoint du compte utilisateur applicatif.
12. **Module 12 : Rapports & Exports Périodiques**
    - Exports filtrables en formats **PDF** haute fidélité et **Excel (.xlsx)** natif au format professionnel pour 4 axes d'analyse : Financier, Clients, Déclarations, Missions.
13. **Module 13 : Recherche Transversale Globale**
    - Moteur de recherche unifié explorant simultanément clients, dossiers, factures, déclarations et documents.
14. **Module 14 : Journal d'Audit & Sécurité**
    - Journalisation immuable de chaque opération sensible avec horodatage, nom de l'opérateur, adresse IP et User-Agent (RG14).

---

## 5. Règles de Gestion Métier Clés (RG01 à RG16)

- **RG01 (Identifiants Marocains) :** L'ICE doit être composé d'exactement 15 chiffres numériques et être unique. Les mentions IF, RC et forme juridique sont obligatoires pour les personnes morales.
- **RG02 (Conservation Logique) :** Les suppressions de clients et dossiers s'effectuent par archivage logique (`SoftDeletes`).
- **RG03 (Référence Dossier) :** Numérotation continue au format `DOS-YYYY-XXXX`.
- **RG04 (Équipe Collaborative) :** Un dossier peut mobiliser plusieurs employés et un employé peut intervenir sur plusieurs dossiers (Relation N:N).
- **RG05 (Superviseur) :** Chaque dossier possède un responsable de mission désigné.
- **RG06 (Numérotation Facture) :** Numérotation chronologique continue sous la forme `FAC-YYYY-XXXX`.
- **RG07 (Calcul Financier) :** Calcul arithmétique strict : $\text{HT} = \sum (\text{Qté} \times \text{PU})$, $\text{TVA} = \text{HT} \times 20\%$, $\text{TTC} = \text{HT} + \text{TVA}$.
- **RG08 (Contrôle d'Encaissement) :** $\text{Paiement} \le \text{Solde Restant Dû}$. Tout sur-paiement est rejeté avec blocage transactionnel.
- **RG09 (Périodicités Fiscales) :** Déclarations paramétrées selon les périodicités légales (mensuelle, trimestrielle, annuelle).
- **RG10 (Récépissé SIMPL) :** Toute déclaration déposée requiert la date réelle et la référence de télétransmission DGI.
- **RG11 (Détection Retards) :** Si $\text{Date Limite} < \text{Date du Jour}$ et $\text{Statut} \ne \text{'déposée'}$, bascule automatique à $\text{'en retard'}$ avec notification d'alerte.
- **RG12 (Intégrité GED) :** Taille maximale de 10 Mo, formats autorisés et rattachement obligatoire à une entité cliente ou mission.
- **RG13 (Échéancier) :** Échéances paramétrées avec degrés de priorité et délais d'alerte.
- **RG14 (Audit Trail) :** Traçabilité exhaustive et inaltérable des créations, modifications, paiements et suppressions.
- **RG15 (Contrôle RBAC) :** Sécurisation backend des routes selon la matrice des rôles.
- **RG16 (Exports de Données) :** Données exportables en classeurs Excel (.xlsx) natifs et rapports PDF A4.

---

## 6. Architecture Technique

- **Framework :** Laravel 12.x (PHP 8.2+).
- **Pattern :** MVC enrichi d'une couche `Service Layer` (`InvoiceService`, `PaymentService`, `DeclarationService`, `DocumentService`).
- **Frontend :** Tailwind CSS, Alpine.js, Chart.js, FontAwesome 6.
- **Moteur PDF :** DomPDF 3.1.2.
- **Base de Données :** Schéma relationnel 3NF de 16 tables avec intégrité référentielle, indexation et suppression en cascade contrôlée.
- **Tests :** Suite de tests unitaires et fonctionnels PHPUnit couvrant 100% des règles critiques.

---

## 7. Critères d'Acceptation & Validation

1. Aucune erreur HTTP 500 sur l'ensemble des routes du système.
2. Rejet effectif des tentatives d'encaissement dépassant le montant restant dû de la facture.
3. Bascule automatique et avérée des déclarations fiscales échues au statut "en retard".
4. Rejet formel des utilisateurs du profil Secrétaire sur les opérations financières et d'administration.
5. Génération et téléchargement sans faille des factures et rapports en format PDF.
6. Passage avec succès de la totalité de la suite de tests automatisés.
