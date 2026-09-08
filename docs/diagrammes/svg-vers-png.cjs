/*
 * Convertit un SVG en PNG haute definition, pour insertion dans le rapport Word.
 *
 * Usage : node svg-vers-png.js entree.svg sortie.png [echelle]
 */

const fs = require('fs');
const path = require('path');
const puppeteer = require('puppeteer');

async function convertir(entree, sortie, echelle) {
    const svg = fs.readFileSync(entree, 'utf8');

    // Dimensions declarees dans la balise <svg>
    const largeur = parseFloat((svg.match(/width="([\d.]+)"/) || [])[1] || 1200);
    const hauteur = parseFloat((svg.match(/height="([\d.]+)"/) || [])[1] || 900);

    const navigateur = await puppeteer.launch({
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    try {
        const page = await navigateur.newPage();
        await page.setViewport({
            width: Math.ceil(largeur),
            height: Math.ceil(hauteur),
            deviceScaleFactor: echelle,
        });

        const html = `<!DOCTYPE html><html><head><meta charset="utf-8">
            <style>html,body{margin:0;padding:0;background:#fff}svg{display:block}</style>
            </head><body>${svg}</body></html>`;

        await page.setContent(html, { waitUntil: 'networkidle0' });
        await page.screenshot({ path: sortie, omitBackground: false });

        console.log(`${path.basename(sortie)} : ${Math.ceil(largeur * echelle)}x${Math.ceil(hauteur * echelle)} px`);
    } finally {
        await navigateur.close();
    }
}

const [entree, sortie, echelle] = process.argv.slice(2);

if (!entree || !sortie) {
    console.error('Usage : node svg-vers-png.js entree.svg sortie.png [echelle]');
    process.exit(1);
}

convertir(entree, sortie, parseFloat(echelle) || 2).catch((e) => {
    console.error(e.message);
    process.exit(1);
});
