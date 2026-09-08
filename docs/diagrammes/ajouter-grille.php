<?php

/*
 * Met en forme les diagrammes SVG rendus par Mermaid selon la présentation
 * classique des ateliers de modélisation UML retenue pour le rapport :
 *
 *   1. ajout d'un fond quadrillé (papier millimétré) ;
 *   2. normalisation monochrome des teintes que Mermaid inscrit directement
 *      en attributs dans le SVG et qui échappent donc au thème ;
 *   3. lignes de vie des diagrammes de séquence tracées en pointillés.
 *
 * Usage : php ajouter-grille.php fichier1.svg [fichier2.svg ...]
 */

const PAS_FIN = 20;      // maille fine, en unités du document
const PAS_GROS = 100;    // maille large
const COULEUR_FINE = '#d3dde6';
const COULEUR_GROSSE = '#b4c4d2';

function injecterGrille(string $chemin): bool
{
    $svg = file_get_contents($chemin);

    if ($svg === false || !str_contains($svg, '<svg')) {
        fwrite(STDERR, "Fichier illisible ou non SVG : {$chemin}\n");
        return false;
    }

    if (str_contains($svg, 'id="fondQuadrille"')) {
        // Grille déjà présente : on repart du SVG d'origine pour éviter les doublons
        $svg = preg_replace('#<defs id="defsQuadrillage">.*?</defs>#s', '', $svg);
        $svg = preg_replace('#<rect[^>]*fill="url\(#fondQuadrille\)"[^>]*/>#', '', $svg);
    }

    // Zone à couvrir : on suit le viewBox, qui peut avoir une origine négative
    if (preg_match('/viewBox="([-\d.]+)\s+([-\d.]+)\s+([\d.]+)\s+([\d.]+)"/', $svg, $m)) {
        [$x, $y, $largeur, $hauteur] = [(float) $m[1], (float) $m[2], (float) $m[3], (float) $m[4]];
    } else {
        fwrite(STDERR, "viewBox introuvable : {$chemin}\n");
        return false;
    }

    // Marge de sécurité pour que la grille dépasse légèrement le contenu
    $marge = 40;
    $x -= $marge;
    $y -= $marge;
    $largeur += $marge * 2;
    $hauteur += $marge * 2;

    $pasFin = PAS_FIN;
    $pasGros = PAS_GROS;
    $couleurFine = COULEUR_FINE;
    $couleurGrosse = COULEUR_GROSSE;

    $defs = <<<XML
    <defs id="defsQuadrillage">
        <pattern id="quadrillageFin" width="{$pasFin}" height="{$pasFin}" patternUnits="userSpaceOnUse">
            <path d="M {$pasFin} 0 L 0 0 0 {$pasFin}" fill="none" stroke="{$couleurFine}" stroke-width="0.9"/>
        </pattern>
        <pattern id="fondQuadrille" width="{$pasGros}" height="{$pasGros}" patternUnits="userSpaceOnUse">
            <rect width="{$pasGros}" height="{$pasGros}" fill="#ffffff"/>
            <rect width="{$pasGros}" height="{$pasGros}" fill="url(#quadrillageFin)"/>
            <path d="M {$pasGros} 0 L 0 0 0 {$pasGros}" fill="none" stroke="{$couleurGrosse}" stroke-width="1.6"/>
        </pattern>
    </defs>
    <rect x="{$x}" y="{$y}" width="{$largeur}" height="{$hauteur}" fill="url(#fondQuadrille)"/>
XML;

    // Insertion juste après la balise <svg ...>
    $svg = preg_replace('/(<svg\b[^>]*>)/', "$1\n{$defs}\n", $svg, 1);

    $svg = normaliserMonochrome($svg);

    file_put_contents($chemin, $svg);

    return true;
}

/**
 * Ramène en noir et blanc les teintes que Mermaid écrit en dur dans le SVG
 * (fonds des participants, lignes de vie, bordures) et met les lignes de vie
 * en pointillés, conformément à la notation UML classique.
 */
function normaliserMonochrome(string $svg): string
{
    $remplacements = [
        'fill="#eaeaea"' => 'fill="#ffffff"',
        'fill="#ECECFF"' => 'fill="#ffffff"',
        'fill:#e0e0e0'   => 'fill:#ffffff',
        'fill:#eaeaea'   => 'fill:#ffffff',
        'stroke="#666"'  => 'stroke="#000000"',
        'stroke="#999"'  => 'stroke="#000000"',
        'stroke:#666'    => 'stroke:#000000',
        'stroke:#999'    => 'stroke:#000000',
    ];

    $svg = str_replace(array_keys($remplacements), array_values($remplacements), $svg);

    // Lignes de vie en pointillés (diagrammes de séquence)
    $svg = preg_replace(
        '/(<line\b[^>]*class="actor-line[^"]*")/',
        '$1 stroke-dasharray="5 4"',
        $svg
    );

    return $svg;
}

$fichiers = array_slice($argv, 1);

if (empty($fichiers)) {
    fwrite(STDERR, "Usage : php ajouter-grille.php fichier1.svg [fichier2.svg ...]\n");
    exit(1);
}

$ok = 0;
foreach ($fichiers as $fichier) {
    if (injecterGrille($fichier)) {
        echo "Grille ajoutée : " . basename($fichier) . PHP_EOL;
        $ok++;
    }
}

exit($ok === count($fichiers) ? 0 : 1);
