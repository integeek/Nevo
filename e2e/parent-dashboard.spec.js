const { test, expect, chromium } = require('@playwright/test');

const CLEANUP_URL = 'http://127.0.0.1/Nevo/e2e/cleanup.php';
const SIGNUP_URL = 'http://127.0.0.1/Nevo/View/Page/SignupParent.php';
const LOGIN_URL = 'http://127.0.0.1/Nevo/View/Page/LoginParent.php';
const DASHBOARD_URL = 'http://127.0.0.1/Nevo/View/Page/ParentDashboard.php';
const EMAIL = 'test_playwright@test.com';
const PASSWORD = 'TestPassword1';

test('redirects to login if not logged in', async ({ page }) => {
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/LoginParent/);
});

test('dashboard loads after login', async ({ page }) => {
    await page.goto(CLEANUP_URL);
    await page.goto(SIGNUP_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="name"]', 'Playwright User');
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-register');
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-login');
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/ParentDashboard/);
});

test('dashboard shows stats cards', async ({ page }) => {
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-login');
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#sCompletion')).toBeVisible();
    await expect(page.locator('#sStreak')).toBeVisible();
});

test('settings link navigates to SettingParent', async ({ page }) => {
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-login');
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a[href="SettingParent.php"]');
    await expect(page).toHaveURL(/SettingParent/);
});

test('family link navigates to SettingFamily', async ({ page }) => {
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-login');
    await page.goto(DASHBOARD_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a[href="SettingFamily.php"]');
    await expect(page).toHaveURL(/SettingFamily/);
});
