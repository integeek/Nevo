const { test, expect } = require('@playwright/test');

const CLEANUP_URL = 'http://127.0.0.1/Nevo/e2e/cleanup.php';
const SIGNUP_URL = 'http://127.0.0.1/Nevo/View/Page/SignupParent.php';

test.beforeAll(async ({ browser }) => {
    const page = await browser.newPage();
    await page.goto(CLEANUP_URL);
    await page.close();
});
const LOGIN_URL = 'http://127.0.0.1/Nevo/View/Page/LoginParent.php';
const EMAIL = 'test_playwright@test.com';
const PASSWORD = 'TestPassword1';

test('signup with valid data leaves the signup page', async ({ page }) => {
    await page.goto(SIGNUP_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="name"]', 'Playwright User');
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-register');
    await expect(page).not.toHaveURL(/SignupParent/);
});

test('signup with existing email shows error', async ({ page }) => {
    await page.goto(SIGNUP_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="name"]', 'Playwright User');
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-register');
    await expect(page.locator('text=This email address is already use')).toBeVisible();
});

test('login with valid credentials leaves the login page', async ({ page }) => {
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-login');
    await expect(page).not.toHaveURL(/LoginParent/);
});

test('login with wrong password shows error', async ({ page }) => {
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', EMAIL);
    await page.fill('input[name="password"]', 'WrongPassword1');
    await page.click('button.btn-login');
    await expect(page.locator('text=The username and/or password does not exist')).toBeVisible();
});

test('login with unknown email shows error', async ({ page }) => {
    await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
    await page.fill('input[name="email"]', 'nobody@test.com');
    await page.fill('input[name="password"]', PASSWORD);
    await page.click('button.btn-login');
    await expect(page.locator('text=The username and/or password does not exist')).toBeVisible();
});
