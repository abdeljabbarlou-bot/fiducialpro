import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

/*
 * Alpine.js — interactions de l'interface (modales, éditeur dynamique de lignes
 * de facture, menus). Exposé sur window pour rester compatible avec les
 * directives x-data déclarées directement dans les vues Blade.
 */
window.Alpine = Alpine;
Alpine.start();

/*
 * Chart.js — graphiques du tableau de bord (évolution du CA, répartition des
 * dossiers, statuts des factures et des déclarations).
 */
window.Chart = Chart;
