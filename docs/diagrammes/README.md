# Diagrammes UML — FiducialPro

Diagrammes de conception du système, établis à partir du code effectivement
implémenté : statuts réels, règles de gestion appliquées et contrôles d'accès
tels qu'ils figurent dans les Policies.

## Jeu principal — un diagramme par type

| Fichier | Type | Objet |
| :--- | :--- | :--- |
| `01-cas-utilisation.svg` | Cas d'utilisation | Acteurs, périmètre fonctionnel et hiérarchie des droits (généralisation Administrateur → Gérant → Comptable) |
| `02-diagramme-de-classes.svg` | Classes | Modèle du domaine : 16 classes, attributs, méthodes métier et cardinalités |
| `03-diagramme-de-sequence.svg` | Séquence | Cycle complet : émission de la facture (RG06, RG07) puis encaissement sous verrou pessimiste (RG08) |
| `04-diagramme-transition-etats.svg` | Transition d'états | Cycle de vie d'une facture d'honoraires (RG07) |
| `05-diagramme-activite.svg` | Activité | Processus du cabinet : prise en charge du client, puis traitement fiscal et facturation menés en parallèle |

## Annexes

Le dossier `annexes/` conserve des variantes plus détaillées, utiles pour
approfondir un point précis en séance de questions :

- séquences dédiées à l'émission de facture, à l'encaissement, au télé-dépôt
  SIMPL et à l'authentification anti-force brute ;
- diagramme d'états de l'obligation fiscale ;
- diagrammes d'activité séparés (facturation, obligation fiscale).

Ces fichiers ne font pas partie du jeu principal et peuvent être supprimés
sans conséquence.

## Formats

- **`.svg`** — diagrammes vectoriels, à insérer directement dans le rapport
  (Word, LibreOffice, PowerPoint). Restent nets à tout niveau de zoom et à
  l'impression.
- **`.mmd`** — sources Mermaid versionnées, afin que chaque diagramme reste
  modifiable. Le diagramme de cas d'utilisation est un SVG rédigé directement
  (Mermaid ne gère pas ce type de diagramme).

## Régénérer un diagramme après modification de sa source

```bash
npx mmdc -i docs/diagrammes/04-diagramme-transition-etats.mmd \
         -o docs/diagrammes/04-diagramme-transition-etats.svg \
         -c docs/diagrammes/mermaid-theme.json -b white
```

Le fichier `mermaid-theme.json` porte la charte graphique commune (couleurs,
police, espacements) qui garantit l'homogénéité de l'ensemble.
