<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité légale du cabinet fiduciaire
    |--------------------------------------------------------------------------
    |
    | Ces informations constituent les mentions légales obligatoires figurant
    | sur toute facture, tout reçu de règlement et tout rapport officiel émis
    | par le cabinet (Code Général des Impôts marocain).
    |
    | Elles sont centralisées ici afin qu'un changement d'adresse, de RIB ou
    | d'identifiant fiscal ne nécessite qu'une seule modification.
    |
    */

    'name' => env('CABINET_NAME', 'FiducialPro'),
    'tagline' => env('CABINET_TAGLINE', "Cabinet d'Expertise Comptable, Audit & Conseil Fiscal"),

    'address' => env('CABINET_ADDRESS', "120 Boulevard d'Anfa, 5ème étage"),
    'city' => env('CABINET_CITY', 'Casablanca'),
    'country' => env('CABINET_COUNTRY', 'Maroc'),

    'phone' => env('CABINET_PHONE', '+212 5 22 10 20 30'),
    'email' => env('CABINET_EMAIL', 'contact@cabinet.ma'),

    /*
    | Identifiants légaux marocains
    */
    'ice' => env('CABINET_ICE', '001548792000034'),
    'if' => env('CABINET_IF', '24589630'),
    'rc' => env('CABINET_RC', '89456'),
    'patente' => env('CABINET_PATENTE', '37850214'),
    'cnss' => env('CABINET_CNSS', '2458963'),

    /*
    | Coordonnées bancaires pour les règlements par virement
    */
    'bank_name' => env('CABINET_BANK_NAME', 'Attijariwafa Bank'),
    'bank_rib' => env('CABINET_BANK_RIB', '007 780 0001234567890123 45'),

    /*
    | Mention de bas de page (ordre professionnel)
    */
    'footer_mention' => env(
        'CABINET_FOOTER_MENTION',
        "Société fiduciaire d'expertise comptable inscrite à l'Ordre des Experts Comptables"
    ),

    /*
    |--------------------------------------------------------------------------
    | Taux de TVA légaux en vigueur au Maroc
    |--------------------------------------------------------------------------
    |
    | Taux normal (20%), taux réduits (14%, 10%, 7%) et exonération (0%)
    | prévus par le Code Général des Impôts. Utilisés à la fois pour la
    | validation des lignes de facture et pour l'affichage des formulaires.
    |
    */
    'tva_rates' => [20, 14, 10, 7, 0],

];
