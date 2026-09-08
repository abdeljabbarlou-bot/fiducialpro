# LIVRABLE 8 : MANUEL D'UTILISATION & GUIDE OPÉRATIONNEL

**Projet :** FiducialPro - Plateforme Métier pour Cabinet Fiduciaire  
**Public Cible :** Administrateurs, Dirigeants/Gérants, Comptables/Chefs de mission, Secrétaires  

---

## 1. Connexion à la Plateforme

1. Accédez à l'adresse web de l'application : `http://127.0.0.1:8000/login`.
2. Saisissez votre adresse email professionnelle et votre mot de passe.
3. *Raccourcis de test (démonstration) :* La page de connexion met à votre disposition des boutons d'accès rapide en un clic pour tester immédiatement chacun des 4 rôles pré-configurés :
   - **Administrateur :** `admin@cabinet.ma` / `password`
   - **Gérant Associé :** `gerant@cabinet.ma` / `password`
   - **Comptable Senior :** `comptable1@cabinet.ma` / `password`
   - **Comptable Junior :** `comptable2@cabinet.ma` / `password`
   - **Secrétaire :** `secretaire@cabinet.ma` / `password`

---

## 2. Guide par Profil Utilisateur

### Profil A : Gérant / Direction du Cabinet

#### 1. Consultation du Tableau de Bord Stratégique (Dashboard)
- Dès la connexion, visualisez instantanément les indicateurs financiers clés :
  * **Chiffre d'Affaires Global :** Total facturé HT et TTC.
  * **Taux de Recouvrement :** Montant total encaissé et solde restant dû (créances clients).
  * **Portefeuille Actif :** Nombre de clients sous contrat et répartition des dossiers par nature de prestation.
  * **Alertes Réglementaires :** Compteur en temps réel des déclarations en retard (RG11) et échéances imminentes (15 jours).
- Analysez les graphiques dynamiques :
  * **Histogramme du CA mensuel :** Évolution des facturations sur les 6 derniers mois.
  * **Diagramme circulaire :** Répartition des dossiers par pôle d'activité (Comptabilité, Fiscalité, Juridique, Création).

#### 2. Génération des Rapports Périodiques & Balances
1. Cliquez sur le menu latéral **Rapports**.
2. Choisissez le type d'analyse :
   - *Rapport Financier :* Sélectionnez la période (ex: du 01/01 au 31/12) et cliquez sur **Export PDF** ou **Export Excel**.
   - *Rapport Portefeuille Clients :* Filtrez par forme juridique (SARL, SA...) et téléchargez la synthèse.
   - *Rapport Déclarations Fiscales :* Générez l'état récapitulatif des télédéclarations SIMPL.

---

### Profil B : Chef de Mission & Collaborateur Comptable

#### 1. Création et Suivi d'un Dossier Client (RG03, RG04)
1. Rendez-vous sur le module **Dossiers** via la barre latérale.
2. Cliquez sur le bouton bleu **Nouveau Dossier**.
3. Remplissez le formulaire :
   - Sélectionnez le client débiteur.
   - Définissez la nature de la mission (Comptabilité générale, Tenue TVA, Constitution...).
   - Indiquez les dates de début et d'échéance ainsi que la priorité.
   - Désignez le superviseur et affectez un premier collaborateur.
4. Cliquez sur **Créer le dossier**. Le système attribue automatiquement une référence unique sous le format `DOS-YYYY-XXXX`.

#### 2. Affectation Multi-Collaborateurs (N:N)
1. Ouvrez la fiche du dossier créé.
2. Dans le volet latéral droit *Équipe Affectée*, cliquez sur le bouton **Affecter**.
3. Choisissez le collaborateur dans la liste déroulante et précisez sa tâche spécifique (ex: *Saisie des achats & rapprochement bancaire*, *Établissement du bilan fiscal*).
4. Cliquez sur **Valider l'affectation**. Le dossier apparaîtra instantanément dans l'espace de travail du collaborateur concerné.

#### 3. Gestion des Déclarations Fiscales & Télédéclaration SIMPL (RG09, RG10, RG11)
1. Accédez au menu **Déclarations**.
2. Les déclarations dont la date limite légale est dépassée apparaissent immédiatement avec un badge clignotant rouge **En retard**.
3. Lors du dépôt effectif de la déclaration sur le portail de la DGI (*SIMPL-TVA* ou *SIMPL-IS*) :
   - Cliquez sur le bouton vert **Dépôt** en face de la ligne concernée.
   - Indiquez la date réelle du dépôt et saisissez le numéro de référence du récépissé électronique délivré par l'administration fiscale.
   - Cliquez sur **Valider comme déposée**. La déclaration passe au statut vert **Déposée** et une notification est automatiquement générée.

#### 4. Facturation des Honoraires & Enregistrement des Règlements (RG06, RG07, RG08)
1. Dans le menu **Factures**, cliquez sur **Émettre une Facture**.
2. Sélectionnez le client. Le système charge automatiquement son ICE, son IF et son adresse de facturation.
3. Renseignez les lignes de prestations :
   - Libellé de la prestation (ex: *Honoraires de tenue comptable - Mois d'avril 2026*).
   - Quantité et Prix Unitaire Hors Taxes.
   - Taux de TVA applicable (20% par défaut au Maroc).
   - *Alpine.js* calcule instantanément le Total HT, la TVA et le Total TTC.
4. Validez l'émission. La facture est numérotée selon la séquence légale continue `FAC-YYYY-XXXX`.
5. Pour imprimer ou télécharger le document :
   - Cliquez sur **Télécharger PDF** pour obtenir la facture officielle au format A4 aux normes comptables marocaines.
   - Cliquez sur **Imprimer** pour une impression directe depuis votre navigateur.
6. Enregistrement d'un règlement :
   - Sur la facture concernée, cliquez sur **Enregistrer Règlement**.
   - Le montant restant dû est pré-rempli. Saisissez le montant versé par le client, le mode (virement, chèque), la banque et la référence du chèque.
   - *Contrôle automatique RG08 :* Si le montant saisi dépasse le solde restant dû, le système bloque la validation avec un avertissement de sécurité.
   - Après validation, cliquez sur **Reçu** pour imprimer le reçu officiel de caisse acquitté destiné au client.

---

### Profil C : Secrétaire & Accueil Administratif

#### 1. Enregistrement d'un Nouveau Client (RG01)
1. Accédez à la rubrique **Clients** puis cliquez sur **Nouveau Client**.
2. Renseignez les mentions légales de l'entreprise :
   - Raison sociale et enseigne commerciale.
   - Forme juridique (SARL, SARL AU, SA, Personne physique...).
   - **ICE (Identifiant Commun de l'Entreprise) :** 15 chiffres obligatoires.
   - Identifiant Fiscal (IF) et Numéro de Registre de Commerce (RC).
   - Coordonnées du siège social, téléphone et adresse email.
   - Nom, prénom, CIN et fonction du gérant ou interlocuteur privilégié.
3. Cliquez sur **Enregistrer le client**.

#### 2. Dépôt et Archivage dans la GED (RG12)
1. Accédez au menu **Documents GED** puis cliquez sur **Archiver un Document**.
2. Glissez-déposez le fichier numérisé (contrat de bail, statuts notariés, modèle J, relevé bancaire).
3. Choisissez la catégorie correspondante et sélectionnez obligatoirement le client ou la mission concernée.
4. Cliquez sur **Archiver dans la GED**. Le fichier est sécurisé et téléchargeable à tout moment par les comptables du cabinet.

#### 3. Planification du Calendrier des Échéances
1. Accédez au module **Échéancier**.
2. Cliquez sur le bouton **Planifier Échéance** pour noter un rendez-vous client, une date de relance de pièces manquantes ou un dépôt au tribunal.
3. Utilisez le commutateur **Calendrier / Liste** pour visualiser le planning sous forme de grille mensuelle interactive.

---

### Profil D : Administrateur Système

#### 1. Gestion des Utilisateurs et Rôles d'Accès (RBAC)
1. Rendez-vous sur le menu **Utilisateurs** (accessible exclusivement aux administrateurs).
2. Cliquez sur **Créer un Utilisateur** pour ouvrir un compte à une nouvelle recrue :
   - Nom, email, mot de passe initial.
   - Attribution du rôle applicatif : *Administrateur*, *Gérant*, *Comptable*, ou *Secrétaire*.
   - Liaison optionnelle avec la fiche collaborateur RH.
3. Pour suspendre un accès en cas de départ d'un employé, cliquez sur l'icône **Désactiver** pour couper immédiatement les accès sans supprimer l'historique des opérations passées.

#### 2. Exploration du Journal d'Audit (RG14)
1. Accédez à la page **Journal d'Audit**.
2. Visualisez la traçabilité intégrale : chaque ajout, modification de facture, suppression de document et connexion est consigné avec horodatage, nom de l'opérateur et adresse IP.
3. Filtrez par module (*invoices*, *declarations*, *clients*) pour mener des investigations de conformité interne.
