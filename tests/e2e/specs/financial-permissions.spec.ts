import { test, expect } from '@playwright/test';

/**
 * Test that users without accounts.read permission cannot see financial data.
 * HR user (hr@test.com) has no accounts.read permission.
 * Super Admin (info@fayazk.com) has all permissions.
 */
test.describe('Financial Data Permissions', () => {
    test.describe('HR User (No accounts.read permission)', () => {
        test.beforeEach(async ({ page }) => {
            // Login as HR user
            await page.goto('/login');
            await page.waitForSelector('input[placeholder*="email"]', { timeout: 10000 });
            await page.fill('input[placeholder*="email"]', 'hr@test.com');
            await page.fill('input[placeholder*="password"]', '@Password1');
            await page.click('button:has-text("Sign in")');
            await page.waitForURL('/dashboard', { timeout: 10000 });
        });

        test('should NOT see Total Cash widgets on dashboard', async ({ page }) => {
            // Wait for page to load
            await page.waitForLoadState('networkidle');

            // Total Cash widgets should NOT be visible
            await expect(page.locator('text=Total Cash')).not.toBeVisible();
        });

        test('should NOT see Distributable Profit on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');
            await expect(page.locator('text=Distributable Profit')).not.toBeVisible();
        });

        test('should NOT see Office Reserve Balance on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');
            await expect(page.locator('text=Office Reserve Balance')).not.toBeVisible();
        });

        test('should NOT see Cash Flow chart on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');
            // Cash Flow chart title should not be visible
            await expect(page.locator('text=Cash Flow')).not.toBeVisible();
        });

        test('should see non-sensitive data on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');

            // Active Employees should be visible
            await expect(page.locator('text=Active Employees')).toBeVisible();

            // Total Receivables should be visible
            await expect(page.locator('text=Total Receivables')).toBeVisible();

            // Invoice Status chart should be visible
            await expect(page.locator('text=Invoice Status')).toBeVisible();
        });

        test('expense form dropdown should NOT show account balances', async ({ page }) => {
            await page.goto('/dashboard/expenses/create');
            await page.waitForLoadState('networkidle');

            // Click on the account dropdown
            await page.click('.ant-select-selector >> nth=0');

            // Wait for dropdown options to appear
            await page.waitForSelector('.ant-select-item-option-content');

            // Get the first option text
            const optionText = await page.locator('.ant-select-item-option-content').first().textContent();

            // Should NOT contain Rs. (PKR currency symbol)
            expect(optionText).not.toContain('Rs.');
        });

        test('transfer create page returns 403 (no transfers permission)', async ({ page }) => {
            const response = await page.goto('/dashboard/transfers/create');
            // Route-level middleware blocks access — HR user has no transfers permissions
            expect(response?.status()).toBe(403);
        });
    });

    test.describe('Super Admin (has accounts.read permission)', () => {
        test.beforeEach(async ({ page }) => {
            // Login as Super Admin
            await page.goto('/login');
            await page.waitForSelector('input[placeholder*="email"]', { timeout: 10000 });
            await page.fill('input[placeholder*="email"]', 'info@fayazk.com');
            await page.fill('input[placeholder*="password"]', '@Password1');
            await page.click('button:has-text("Sign in")');
            await page.waitForURL('/dashboard', { timeout: 10000 });
        });

        test('should see Total Cash widgets on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');

            // Total Cash widgets should be visible
            await expect(page.locator('text=Total Cash').first()).toBeVisible();
        });

        test('should see Distributable Profit on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');
            await expect(page.locator('text=Distributable Profit')).toBeVisible();
        });

        test('should see Office Reserve Balance on dashboard', async ({ page }) => {
            await page.waitForLoadState('networkidle');
            await expect(page.locator('text=Office Reserve Balance')).toBeVisible();
        });

        test('expense form dropdown should show account balances', async ({ page }) => {
            await page.goto('/dashboard/expenses/create');
            await page.waitForLoadState('networkidle');

            // Click on the account dropdown
            await page.click('.ant-select-selector >> nth=0');

            // Wait for dropdown options to appear
            await page.waitForSelector('.ant-select-item-option-content');

            // Get the first option text
            const optionText = await page.locator('.ant-select-item-option-content').first().textContent();

            // Should contain a currency symbol (Rs. or $)
            const hasCurrencySymbol = optionText?.includes('Rs.') || optionText?.includes('$');
            expect(hasCurrencySymbol).toBe(true);
        });

        test('transfer form dropdown should show account balances', async ({ page }) => {
            await page.goto('/dashboard/transfers/create');
            await page.waitForLoadState('networkidle');

            // Wait for the select components to be ready
            await page.waitForSelector('.ant-select-selector');

            // Click on the source account dropdown (first select)
            await page.locator('.ant-select-selector').first().click();

            // Wait for dropdown options to appear
            await page.waitForSelector('.ant-select-item-option-content', { timeout: 5000 });

            // Small delay to ensure content is loaded
            await page.waitForTimeout(300);

            // Get all option texts
            const options = await page.locator('.ant-select-item-option-content').allTextContents();

            // At least one option should contain a currency symbol
            const hasBalanceVisible = options.some((opt) => opt.includes('Rs.') || opt.includes('$'));
            expect(hasBalanceVisible).toBe(true);
        });
    });
});
