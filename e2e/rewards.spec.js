const { test, expect } = require('@playwright/test');

const REWARDS_URL = 'http://localhost/View/Page/RewardShop.php';

test('XP value is displayed', async ({ page }) => {
    await page.goto(REWARDS_URL, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#xpDisplay')).toBeVisible();
});

test('reward cards are rendered', async ({ page }) => {
    await page.goto(REWARDS_URL, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('.reward-card');
    const count = await page.locator('.reward-card').count();
    expect(count).toBeGreaterThan(0);
});

test('back button navigates to HomePageChild', async ({ page }) => {
    await page.goto(REWARDS_URL, { waitUntil: 'domcontentloaded' });
    await page.click('.back-btn a');
    await expect(page).toHaveURL(/HomePageChild/);
});
