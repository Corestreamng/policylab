const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const BASE_URL = 'http://127.0.0.1:8000';

const pagesToCapture = [
    { name: 'landing', path: '/' },
    { name: 'dashboard', path: '/dashboard', waitForChart: true },
    { name: 'documents', path: '/documents' },
    { name: 'state_lagos', path: '/states/lagos', waitForDrawer: true },
    { name: 'state_oyo', path: '/states/oyo', waitForDrawer: true },
    { name: 'cookie_banner', path: '/', forceBanner: true },
    { name: 'admin_login', path: '/admin/login' },
    { name: 'admin_dashboard', path: '/admin', auth: true },
    { name: 'admin_bulk_import', path: '/admin/bulk-data-import', auth: true }
];

const widths = [
    { width: 360, height: 800 },
    { width: 768, height: 1024 },
    { width: 1280, height: 900 }
];

async function captureAll() {
    const outDir = path.join(__dirname, '..', 'docs', 'screenshots');
    if (!fs.existsSync(outDir)) {
        fs.mkdirSync(outDir, { recursive: true });
    }

    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    for (const p of pagesToCapture) {
        for (const w of widths) {
            const page = await browser.newPage();
            await page.setViewport({ width: w.width, height: w.height, deviceScaleFactor: 1 });

            // If auth is required, visit /dev-login first
            if (p.auth) {
                await page.goto(`${BASE_URL}/dev-login`, { waitUntil: 'networkidle2' });
            }

            if (p.forceBanner) {
                await page.evaluateOnNewDocument(() => {
                    localStorage.removeItem('ypt_cookie_consent');
                });
            }

            const targetUrl = `${BASE_URL}${p.path}`;
            console.log(`Navigating to ${targetUrl} [${w.width}px]...`);
            await page.goto(targetUrl, { waitUntil: 'networkidle2', timeout: 20000 });

            // Wait for charts on dashboard
            if (p.waitForChart) {
                try {
                    await page.waitForFunction(() => {
                        const c = document.getElementById('stageDistributionChart');
                        return c && c._chartInstance;
                    }, { timeout: 3000 });
                } catch (e) {
                    console.log('Chart wait timed out (proceeding)');
                }
            }

            // Wait for drawer on state pages
            if (p.waitForDrawer) {
                try {
                    await page.waitForSelector('[data-state-drawer]', { timeout: 3000 });
                } catch (e) {
                    // Drawer is present in DOM
                }
            }

            if (p.forceBanner) {
                // Ensure banner is visible
                await page.evaluate(() => {
                    localStorage.removeItem('ypt_cookie_consent');
                    const banner = document.querySelector('[x-show="showConsentBanner"]');
                    if (banner) {
                        banner.style.display = 'block';
                    }
                });
            }

            // Wait a moment for transitions / fonts
            await new Promise(r => setTimeout(r, 400));

            const filename = `${p.name}_${w.width}.png`;
            const filePath = path.join(outDir, filename);

            await page.screenshot({
                path: filePath,
                fullPage: true
            });

            const stat = fs.statSync(filePath);
            console.log(`Saved ${filename} (${stat.size} bytes)`);

            await page.close();
        }
    }

    await browser.close();
    console.log('\nAll 27 full-page screenshots captured successfully!');
}

captureAll().catch(err => {
    console.error('Fatal error capturing screenshots:', err);
    process.exit(1);
});
