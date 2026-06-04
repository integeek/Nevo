const { test, expect } = require('@playwright/test');

const FEELINGS_URL = 'http://localhost/View/Page/WriteFeelings.php';

test('6 feeling buttons are visible', async ({ page }) => {
    await page.goto(FEELINGS_URL, { waitUntil: 'domcontentloaded' });
    const count = await page.locator('.feeling-btn').count();
    expect(count).toBe(6);
});

test('clicking a feeling button selects it', async ({ page }) => {
    await page.goto(FEELINGS_URL, { waitUntil: 'domcontentloaded' });
    await page.click('button[data-feeling="Awesome"]');
    await expect(page.locator('button[data-feeling="Awesome"]')).toHaveClass(/selected/);
});

test('clicking a second feeling deselects the first', async ({ page }) => {
    await page.goto(FEELINGS_URL, { waitUntil: 'domcontentloaded' });
    await page.click('button[data-feeling="Awesome"]');
    await page.click('button[data-feeling="Good"]');
    await expect(page.locator('button[data-feeling="Awesome"]')).not.toHaveClass(/selected/);
    await expect(page.locator('button[data-feeling="Good"]')).toHaveClass(/selected/);
});

test('publishing a feeling updates the recent list', async ({ page }) => {
    await page.goto(FEELINGS_URL, { waitUntil: 'domcontentloaded' });
    await page.click('button[data-feeling="Good"]');
    await page.click('#shareBtn');
    await page.waitForSelector('#recentList .feeling-entry');
    const count = await page.locator('#recentList .feeling-entry').count();
    expect(count).toBeGreaterThan(0);
});

test('back button navigates to HomePageChild', async ({ page }) => {
    await page.goto(FEELINGS_URL, { waitUntil: 'domcontentloaded' });
    await page.click('.back-btn a');
    await expect(page).toHaveURL(/HomePageChild/);
});
