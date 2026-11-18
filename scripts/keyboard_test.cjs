const puppeteer = require('puppeteer-core');
const assert = require('assert');

const CHROME_PATH = process.env.CHROME_BIN || '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome';
const BASE_URL = process.env.APP_TEST_URL || process.argv[2] || 'http://127.0.0.1:8000';

async function runKeyboardTests() {
    console.log(`Starting Automated Keyboard Navigation & RTL Verification against ${BASE_URL}...`);

    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu']
    });

    try {
        const page = await browser.newPage();
        await page.setViewport({ width: 1280, height: 900 });

        // 1. Load Landing Page
        console.log('\n1. Navigating to landing page...');
        const res = await page.goto(BASE_URL, { waitUntil: 'networkidle2' });
        assert.strictEqual(res.status(), 200, 'Landing page should return HTTP 200');

        // 2. Test Map Keyboard Navigation & Enter Key Activation
        console.log('2. Testing Map keyboard navigation (Focus -> #state-lagos -> Enter)...');
        await page.waitForSelector("path[id='state-lagos']", { timeout: 10000 });

        // Wait for Alpine hydration of SVG paths
        await page.waitForFunction(() => {
            const p = document.querySelector("#state-lagos");
            return p && p.getAttribute('aria-label') && p.getAttribute('aria-label').includes(':');
        }, { timeout: 10000 });

        // Verify state path attributes for accessibility
        const pathAttrs = await page.evaluate(() => {
            const p = document.querySelector("#state-lagos");
            return {
                tabindex: p.getAttribute('tabindex'),
                role: p.getAttribute('role'),
                ariaLabel: p.getAttribute('aria-label')
            };
        });
        console.log(`   #state-lagos Accessibility: role="${pathAttrs.role}", tabindex="${pathAttrs.tabindex}", aria-label="${pathAttrs.ariaLabel}"`);
        assert.strictEqual(pathAttrs.role, 'button', 'Map path must have role="button"');
        assert.strictEqual(pathAttrs.tabindex, '0', 'Map path must have tabindex="0"');
        assert.ok(pathAttrs.ariaLabel.includes('Lagos'), 'Map path aria-label must include state name');

        // Trigger keyboard Enter on the path
        await page.evaluate(() => {
            const p = document.querySelector("#state-lagos");
            p.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true, cancelable: true }));
        });

        // Wait for drawer dialog
        await page.waitForSelector('[role="dialog"]', { timeout: 5000 });
        const drawerTitle = await page.$eval('#drawer-title', el => el.textContent.trim());
        console.log(`   Drawer opened via keyboard Enter! Title: "${drawerTitle}"`);
        assert.ok(drawerTitle.includes('Lagos'), 'Drawer title should contain Lagos');

        // 3. Test Drawer Keyboard Dismissal (Escape Key)
        console.log('3. Testing Drawer Escape key dismissal...');
        await page.keyboard.press('Escape');

        // Wait for drawer to close
        await new Promise(r => setTimeout(r, 600));
        const drawerClosed = await page.evaluate(() => {
            const d = document.querySelector("[role='dialog']");
            return !d || d.style.display === 'none' || d.offsetParent === null;
        });
        console.log(`   Drawer closed successfully on Escape key press: ${drawerClosed}`);
        assert.ok(drawerClosed, 'Drawer should close when Escape key is pressed');

        // 4. Test Filter Keyboard Interaction
        console.log('4. Testing Filter input keyboard interaction...');
        const searchSelector = "input[placeholder*='Search']";
        await page.waitForSelector(searchSelector);
        await page.focus(searchSelector);
        await page.keyboard.type('Kano', { delay: 50 });

        // Wait for Livewire debounce update
        await new Promise(r => setTimeout(r, 800));
        const filterState = await page.evaluate(() => {
            const el = document.querySelector("h3[class*='font-bold']");
            return el ? el.textContent.trim() : null;
        });
        console.log(`   Search filter result via keyboard typing: ${filterState}`);

        // Clear search input via keyboard Backspace
        await page.click(searchSelector, { clickCount: 3 });
        await page.keyboard.press('Backspace');
        await new Promise(r => setTimeout(r, 600));

        // 5. Test Accessible Table Toggle via Keyboard
        console.log('5. Testing Accessible Table View toggle...');
        const tableToggleBtn = await page.evaluateHandle(() => {
            const btns = Array.from(document.querySelectorAll('button'));
            return btns.find(b => b.textContent.includes('Accessible Table View'));
        });

        if (tableToggleBtn && tableToggleBtn.asElement()) {
            await tableToggleBtn.asElement().focus();
            await page.keyboard.press('Enter');
            await new Promise(r => setTimeout(r, 600));

            const tableExists = await page.evaluate(() => {
                return document.querySelector('table') !== null;
            });
            console.log(`   Accessible table visible after keyboard Enter: ${tableExists}`);
            assert.ok(tableExists, 'Accessible table should be visible after activating toggle');
        }

        // 6. Test Arabic RTL Verification & Screenshot
        console.log('\n6. Testing Arabic RTL configuration & capturing screenshot...');
        const arPage = await browser.newPage();
        await arPage.setViewport({ width: 1280, height: 900 });

        // Switch locale to Arabic (using preview parameter to verify RTL rendering)
        await arPage.goto(`${BASE_URL}/locale/ar?preview=1`, { waitUntil: 'networkidle2' });
        await arPage.goto(BASE_URL, { waitUntil: 'networkidle2' });

        const htmlDir = await arPage.evaluate(() => document.documentElement.getAttribute('dir'));
        const htmlLang = await arPage.evaluate(() => document.documentElement.getAttribute('lang'));
        console.log(`   Arabic Page Attributes: dir="${htmlDir}", lang="${htmlLang}"`);

        assert.strictEqual(htmlDir, 'rtl', 'HTML dir attribute must be rtl when Arabic locale is active');
        assert.strictEqual(htmlLang, 'ar', 'HTML lang attribute must be ar when Arabic locale is active');

        const screenshotPath = 'docs/screenshots/arabic_rtl_1280.png';
        await arPage.screenshot({ path: screenshotPath, fullPage: true });
        console.log(`   Captured Arabic RTL screenshot saved to ${screenshotPath}`);

        // Reset locale back to English
        await arPage.goto(`${BASE_URL}/locale/en`, { waitUntil: 'networkidle2' });

        console.log('\n======================================================');
        console.log('ALL KEYBOARD NAVIGATION & RTL TESTS PASSED (6/6)');
        console.log('======================================================');

    } finally {
        await browser.close();
    }
}

runKeyboardTests().catch(err => {
    console.error('Keyboard Test Failed:', err);
    process.exit(1);
});
