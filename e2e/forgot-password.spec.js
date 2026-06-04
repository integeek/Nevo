const { test, expect } = require('@playwright/test');

const FORGOT_URL = 'http://localhost/View/Page/ForgotPassword.php';

// test('submitting unknown email shows error', async ({ page }) => {
//     await page.goto(FORGOT_URL, { waitUntil: 'domcontentloaded' });
//     await page.fill('input[name="email"]', 'nobody@unknown.com');
//     await page.click('button.btn-send');
//     await page.waitForURL(/ForgotPassword/);
//     await expect(page.locator('.error')).not.toBeEmpty();
// });

test('back to login link navigates to LoginParent', async ({ page }) => {
    await page.goto(FORGOT_URL, { waitUntil: 'domcontentloaded' });
    await page.click('a.back-link');
    await expect(page).toHaveURL(/LoginParent/);
});
