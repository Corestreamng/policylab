const puppeteer = require('puppeteer-core');
const assert = require('assert');

const CHROME_PATH = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const BASE_URL = process.env.APP_TEST_URL || 'http://127.0.0.1:8000';

const pagesToTest = [
    { name: 'Landing Page', path: '/' },
    { name: 'Analytics Dashboard', path: '/dashboard', checkCharts: true },
    { name: 'Document Vault', path: '/documents' },
    { name: 'State Drawer (Lagos)', path: '/states/lagos' },
    { name: 'Admin Login', path: '/admin/login' },
    { name: 'Admin Dashboard', path: '/admin', auth: true },
    { name: 'Admin Bulk Import', path: '/admin/bulk-data-import', auth: true }
];

async function runSmokeTests() {
    console.log(`Starting Front-End Smoke Tests against ${BASE_URL}...`);

    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    let totalPassed = 0;
    let totalFailed = 0;

    for (const testPage of pagesToTest) {
        console.log(`\nTesting ${testPage.name} (${testPage.path})...`);
        const page = await browser.newPage();
        await page.setViewport({ width: 1280, height: 900 });

        const failedAssets = [];
        page.on('requestfailed', req => {
            const url = req.url();
            if (url.endsWith('.css') || url.endsWith('.js') || url.includes('/build/') || url.includes('/vendor/')) {
                failedAssets.push({ url, error: req.failure() ? req.failure().errorText : 'failed' });
            }
        });

        try {
            if (testPage.auth) {
                await page.goto(`${BASE_URL}/dev-login`, { waitUntil: 'networkidle2' });
            }

            const response = await page.goto(`${BASE_URL}${testPage.path}`, { waitUntil: 'networkidle2', timeout: 15000 });
            assert.strictEqual(response.status(), 200, `${testPage.name} should return HTTP 200`);

            // 1. Assert no failed CSS / JS requests
            assert.strictEqual(
                failedAssets.length,
                0,
                `Failed assets on ${testPage.name}: ${JSON.stringify(failedAssets)}`
            );

            // 2. Assert page has applied stylesheets
            const styleInfo = await page.evaluate(() => {
                const sheetCount = document.styleSheets.length;
                const bodyBg = window.getComputedStyle(document.body).backgroundColor;
                const bodyColor = window.getComputedStyle(document.body).color;
                return { sheetCount, bodyBg, bodyColor };
            });
            assert.ok(styleInfo.sheetCount > 0, `Page ${testPage.name} has no stylesheets loaded`);
            assert.ok(styleInfo.bodyColor, `Page ${testPage.name} has no computed body color`);

            // 3. Assert visible body text does not leak scripts or internal attributes
            const leakedText = await page.evaluate(() => {
                const text = document.body.innerText;
                const matches = [];
                if (text.includes('x-data')) matches.push('x-data');
                if (text.includes('$wire')) matches.push('$wire');
                if (text.includes('setAttribute')) matches.push('setAttribute');
                return matches;
            });
            assert.strictEqual(
                leakedText.length,
                0,
                `Visible script text leaked on ${testPage.name}: ${leakedText.join(', ')}`
            );

            // 4. Assert no icon renders larger than 64px
            const hugeIcons = await page.evaluate(() => {
                const icons = Array.from(document.querySelectorAll('i.fa, i.fas, i.far, svg.fi-icon, svg.filament-icon'));
                return icons.filter(i => {
                    const rect = i.getBoundingClientRect();
                    return rect.width > 64 || rect.height > 64;
                }).map(i => ({
                    tag: i.tagName,
                    class: i.className,
                    width: i.getBoundingClientRect().width,
                    height: i.getBoundingClientRect().height
                }));
            });
            assert.strictEqual(
                hugeIcons.length,
                0,
                `Huge icons found on ${testPage.name}: ${JSON.stringify(hugeIcons)}`
            );

            // 5. If dashboard, assert charts exist and are not empty
            if (testPage.checkCharts) {
                const chartState = await page.evaluate(() => {
                    const stageChart = document.getElementById('stageDistributionChart');
                    const zoneChart = document.getElementById('zoneBreakdownChart');
                    return {
                        hasStageChart: !!stageChart && stageChart.width > 0 && stageChart.height > 0,
                        hasZoneChart: !!zoneChart && zoneChart.width > 0 && zoneChart.height > 0
                    };
                });
                assert.ok(chartState.hasStageChart, 'stageDistributionChart canvas is missing or empty');
                assert.ok(chartState.hasZoneChart, 'zoneBreakdownChart canvas is missing or empty');
            }

            console.log(`PASS: ${testPage.name}`);
            totalPassed++;
        } catch (err) {
            console.error(`FAIL: ${testPage.name} -> ${err.message}`);
            totalFailed++;
        } finally {
            await page.close();
        }
    }

    await browser.close();

    console.log(`\n========================================`);
    console.log(`SMOKE TEST RESULTS: ${totalPassed} passed, ${totalFailed} failed`);
    console.log(`========================================`);

    if (totalFailed > 0) {
        process.exit(1);
    }
}

runSmokeTests();
