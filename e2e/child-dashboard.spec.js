const { test, expect } = require('@playwright/test');

const DASHBOARD_URL = 'http://localhost/View/Page/HomePageChild.php';

test('XP bar is visible on the dashboard', async ({ page }) => {
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.xp-bar-track')).toBeVisible();
});

test('streak stat card is visible', async ({ page }) => {
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#statStreak')).toBeVisible();
});

test('routines list renders at least one item', async ({ page }) => {
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('#routines-list .quest-card', { timeout: 10000 });
    const count = await page.locator('#routines-list .quest-card').count();
    expect(count).toBeGreaterThan(0);
});

test('feelings quick-card navigates to WriteFeelings', async ({ page }) => {
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a[href="WriteFeelings.php"]');
    await expect(page).toHaveURL(/WriteFeelings/);
});

test('rewards quick-card navigates to RewardShop', async ({ page }) => {
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a[href="RewardShop.php"]');
    await expect(page).toHaveURL(/RewardShop/);
});
