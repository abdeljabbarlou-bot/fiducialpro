# Diagrammes UML — FiducialPro

Diagrammes de conception du système, établis à partir du code effectivement
implémenté (statuts réels, règles de gestion, contrôles d'accès appliqués).

| Fichier | Type de diagramme | Objet |
| :--- | :--- | :--- |
| `01-cas-utilisation.svg` | Cas d'utilisation | Acteurs, périmètre fonctionnel et hiérarchie des droits |
| `02-sequence-emission-facture.svg` | Séquence | Émission d'une facture d'honoraires et édition du PDF (RG06, RG07) |
| `03-sequence-encaissement-rg08.svg` | Séquence | Enregistrement d'un règlement sous verrou pessimiste (RG08) |
| `04-sequence-teledepot-simpl.svg` | Séquence | Télé-dépôt d'une déclaration et traçabilité du récépissé DGI (RG10) |
| `05-sequence-authentification.svg` | Séquence | Authentification et protection contre la force brute (RG15) |
| `06-etats-facture.svg` | Transition d'états | Cycle de vie d'une facture (RG07) |
| `07-etats-declaration.svg` | Transition d'états | Cycle de vie d'une obligation fiscale (RG09, RG11) |
| `08-activite-cycle-facturation.svg` | Activité | Processus complet de facturation et de recouvrement |
| `09-activite-obligation-fiscale.svg` | Activité | Processus de traitement d'une obligation fiscale |

## Formats

- **`.svg`** — diagrammes vectoriels, à insérer directement dans le rapport
  (Word, LibreOffice, PowerPoint) ou dans un document PDF. Restent nets à
  n'importe quel niveau de zoom et à l'impression.
- **`.mmd`** — sources Mermaid des diagrammes 02 à 09, versionnées afin que
  chaque diagramme reste modifiable. Le diagramme 01 est un SVG rédigé
  directement (Mermaid ne gère pas les diagrammes de cas d'utilisation).

## Régénérer les diagrammes après modification d'une source

```bash
npx mmdc -i docs/diagrammes/06-etats-facture.mmd \
         -o docs/diagrammes/06-etats-facture.svg \
         -c docs/diagrammes/mermaid-theme.json -b white
```

Le fichier `mermaid-theme.json` contient la charte graphique commune
(couleurs, police, espacements) afin que tous les diagrammes restent homogènes.
