const { test, expect } = require('@playwright/test');

const HOME_LOGIN_URL = 'http://localhost/View/Page/HomeLogin.php';

test('clicking parent card navigates away from HomeLogin', async ({ page }) => {
    await page.goto(HOME_LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a#parent-card');
    await expect(page).not.toHaveURL(/HomeLogin/);
});

test('clicking child card navigates away from HomeLogin', async ({ page }) => {
    await page.goto(HOME_LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a#hero-card');
    await expect(page).not.toHaveURL(/HomeLogin/);
});
