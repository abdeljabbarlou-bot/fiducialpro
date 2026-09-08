/*
 * Capture les principales interfaces de l'application pour illustrer le
 * chapitre « Realisation » du rapport.
 *
 * Prerequis : le serveur de developpement doit tourner (php artisan serve).
 * Usage : node docs/captures/capturer.cjs
 */

const path = require('path');
const puppeteer = require('puppeteer');

const BASE = 'http://localhost:8000';
const DOSSIER = __dirname;

const PAGES = [
    { fichier: '02-tableau-de-bord.png', url: '/dashboard', attendre: 'canvas' },
    { fichier: '03-portefeuille-clients.png', url: '/clients' },
    { fichier: '04-fiche-client.png', url: '/clients/1' },
    { fichier: '05-dossiers.png', url: '/dossiers' },
    { fichier: '06-declarations.png', url: '/declarations' },
    { fichier: '07-facture.png', url: '/invoices/1' },
    { fichier: '08-nouvelle-facture.png', url: '/invoices/create' },
    { fichier: '09-paiements.png', url: '/payments' },
    { fichier: '10-ged-documents.png', url: '/documents' },
    { fichier: '11-rapports.png', url: '/reports' },
    { fichier: '12-journal-audit.png', url: '/activity-logs' },
    { fichier: '13-corbeille.png', url: '/corbeille' },
    { fichier: '14-utilisateurs.png', url: '/users' },
];

(async () => {
    const navigateur = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    try {
        const page = await navigateur.newPage();
        await page.setViewport({ width: 1440, height: 900, deviceScaleFactor: 2 });

        // --- Page de connexion (avant authentification) ---
        await page.goto(`${BASE}/login`, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: path.join(DOSSIER, '01-connexion.png') });
        console.log('01-connexion.png');

        // --- Authentification ---
        // Les champs sont pre-remplis par la vue : on les reecrit entierement
        await page.evaluate(() => {
            document.querySelector('input[name="email"]').value = 'admin@cabinet.ma';
            document.querySelector('input[name="password"]').value = 'password';
        });
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'networkidle0' }),
            page.evaluate(() => document.getElementById('loginForm').submit()),
        ]);

        if (page.url().includes('/login')) {
            throw new Error("l'authentification a echoue, capture interrompue");
        }

        // --- Pages internes ---
        for (const { fichier, url, attendre } of PAGES) {
            await page.goto(BASE + url, { waitUntil: 'networkidle0' });

            if (attendre) {
                await page.waitForSelector(attendre, { timeout: 10000 }).catch(() => {});
                // Laisse le temps aux graphiques de se dessiner
                await new Promise((r) => setTimeout(r, 1200));
            }

            await page.screenshot({ path: path.join(DOSSIER, fichier) });
            console.log(fichier);
        }
    } finally {
        await navigateur.close();
    }
})().catch((e) => {
    console.error('Echec :', e.message);
    process.exit(1);
});
