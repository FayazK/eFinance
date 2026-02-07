import { test, expect, Page } from '@playwright/test';

/**
 * Comprehensive E2E tests for the RBAC permissions system.
 *
 * Test users:
 *   - Super Admin: info@fayazk.com (all permissions)
 *   - HR User: hr@test.com (employees.*, expenses.*, payroll.* — no access to other modules)
 */

async function loginAs(page: Page, email: string, password: string): Promise<void> {
    await page.goto('/login');
    await page.waitForSelector('input[placeholder*="email"]', { timeout: 10000 });
    await page.fill('input[placeholder*="email"]', email);
    await page.fill('input[placeholder*="password"]', password);
    await page.click('button:has-text("Sign in")');
    await page.waitForURL('/dashboard', { timeout: 10000 });
    await page.waitForLoadState('networkidle');
}

const SUPER_ADMIN = { email: 'info@fayazk.com', password: '@Password1' };
const HR_USER = { email: 'hr@test.com', password: '@Password1' };

// ─────────────────────────────────────────────────────────────
// Super Admin — Full Access
// ─────────────────────────────────────────────────────────────
test.describe('Super Admin - Full Access', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, SUPER_ADMIN.email, SUPER_ADMIN.password);
    });

    test('sees all navigation groups', async ({ page }) => {
        const navGroups = ['CRM', 'Billing', 'Finance', 'HR', 'Equity', 'Administration'];
        for (const group of navGroups) {
            await expect(page.getByText(group, { exact: true }).first()).toBeVisible();
        }
    });

    test('sees all navigation items', async ({ page }) => {
        const navItems = [
            'Clients', 'Contacts', 'Projects',
            'Invoices', 'Companies',
            'Accounts', 'Transfers', 'Expenses',
            'Employees', 'Payroll',
            'Shareholders', 'Distributions',
            'Users', 'Roles',
        ];
        for (const item of navItems) {
            await expect(page.getByText(item, { exact: true }).first()).toBeVisible();
        }
    });

    test('sees all financial widgets on dashboard', async ({ page }) => {
        await expect(page.getByText('Total Cash (PKR)').first()).toBeVisible();
        await expect(page.getByText('Total Cash (USD)').first()).toBeVisible();
        await expect(page.getByText('Distributable Profit').first()).toBeVisible();
        await expect(page.getByText('Runway').first()).toBeVisible();
        await expect(page.getByText('Office Reserve Balance').first()).toBeVisible();
        await expect(page.getByText('Cash Flow Trend (6 Months)').first()).toBeVisible();
        await expect(page.getByText('Recent Transactions').first()).toBeVisible();
    });

    test('sees non-sensitive widgets on dashboard', async ({ page }) => {
        await expect(page.getByText('Total Receivables').first()).toBeVisible();
        await expect(page.getByText('Active Employees').first()).toBeVisible();
        await expect(page.getByText('Invoice Status').first()).toBeVisible();
    });

    test('can access Roles page', async ({ page }) => {
        await page.goto('/dashboard/roles');
        await page.waitForLoadState('networkidle');
        await expect(page.getByText('Roles').first()).toBeVisible();
        await expect(page.getByText('Add Role')).toBeVisible();
    });

    test('can access Users page', async ({ page }) => {
        await page.goto('/dashboard/users');
        await page.waitForLoadState('networkidle');
        // Page loads without 403
        await expect(page).not.toHaveURL(/login/);
    });

    test('expense form dropdown shows account balances', async ({ page }) => {
        await page.goto('/dashboard/expenses/create');
        await page.waitForLoadState('networkidle');

        await page.click('.ant-select-selector >> nth=0');
        await page.waitForSelector('.ant-select-item-option-content');

        const options = await page.locator('.ant-select-item-option-content').allTextContents();
        const hasBalance = options.some((opt) => opt.includes('Rs.') || opt.includes('$'));
        expect(hasBalance).toBe(true);
    });
});

// ─────────────────────────────────────────────────────────────
// HR User — Restricted Access (Navigation)
// ─────────────────────────────────────────────────────────────
test.describe('HR User - Restricted Navigation', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, HR_USER.email, HR_USER.password);
    });

    test('sees only permitted nav items: Expenses, Employees, Payroll', async ({ page }) => {
        await expect(page.getByText('Expenses', { exact: true }).first()).toBeVisible();
        await expect(page.getByText('Employees', { exact: true }).first()).toBeVisible();
        await expect(page.getByText('Payroll', { exact: true }).first()).toBeVisible();
    });

    test('does NOT see CRM nav group', async ({ page }) => {
        await expect(page.getByText('CRM', { exact: true })).not.toBeVisible();
        await expect(page.locator('nav').getByText('Clients')).not.toBeVisible();
        await expect(page.locator('nav').getByText('Contacts')).not.toBeVisible();
        await expect(page.locator('nav').getByText('Projects')).not.toBeVisible();
    });

    test('does NOT see Billing nav group', async ({ page }) => {
        await expect(page.getByText('Billing', { exact: true })).not.toBeVisible();
        await expect(page.locator('nav').getByText('Invoices')).not.toBeVisible();
        await expect(page.locator('nav').getByText('Companies')).not.toBeVisible();
    });

    test('does NOT see Accounts or Transfers in Finance', async ({ page }) => {
        await expect(page.locator('nav').getByText('Accounts')).not.toBeVisible();
        await expect(page.locator('nav').getByText('Transfers')).not.toBeVisible();
    });

    test('does NOT see Equity nav group', async ({ page }) => {
        await expect(page.getByText('Equity', { exact: true })).not.toBeVisible();
        await expect(page.locator('nav').getByText('Shareholders')).not.toBeVisible();
        await expect(page.locator('nav').getByText('Distributions')).not.toBeVisible();
    });

    test('does NOT see Administration nav group', async ({ page }) => {
        await expect(page.getByText('Administration', { exact: true })).not.toBeVisible();
        await expect(page.locator('nav').getByText('Users')).not.toBeVisible();
        await expect(page.locator('nav').getByText('Roles')).not.toBeVisible();
    });
});

// ─────────────────────────────────────────────────────────────
// HR User — Restricted Access (Dashboard Widgets)
// ─────────────────────────────────────────────────────────────
test.describe('HR User - Dashboard Widget Visibility', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, HR_USER.email, HR_USER.password);
    });

    test('hides financial widgets', async ({ page }) => {
        await expect(page.getByText('Total Cash')).not.toBeVisible();
        await expect(page.getByText('Distributable Profit')).not.toBeVisible();
        await expect(page.getByText('Runway')).not.toBeVisible();
        await expect(page.getByText('Office Reserve Balance')).not.toBeVisible();
        await expect(page.getByText('Cash Flow')).not.toBeVisible();
        await expect(page.getByText('Recent Transactions')).not.toBeVisible();
    });

    test('shows non-sensitive data', async ({ page }) => {
        await expect(page.getByText('Total Receivables').first()).toBeVisible();
        await expect(page.getByText('Active Employees').first()).toBeVisible();
        await expect(page.getByText('Invoice Status').first()).toBeVisible();
    });
});

// ─────────────────────────────────────────────────────────────
// HR User — Route-Level Permission Enforcement
// ─────────────────────────────────────────────────────────────
test.describe('HR User - Route-Level Permission Enforcement', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, HR_USER.email, HR_USER.password);
    });

    test('expense create page loads (has expenses.create permission)', async ({ page }) => {
        const response = await page.goto('/dashboard/expenses/create');
        expect(response?.status()).toBe(200);
    });

    test('transfer create page returns 403 (no transfers.create permission)', async ({ page }) => {
        const response = await page.goto('/dashboard/transfers/create');
        expect(response?.status()).toBe(403);
    });
});

// ─────────────────────────────────────────────────────────────
// HR User — Direct URL Access (Security Gap Documentation)
// ─────────────────────────────────────────────────────────────
test.describe('HR User - Direct URL Access', () => {
    test.beforeEach(async ({ page }) => {
        await loginAs(page, HR_USER.email, HR_USER.password);
    });

    test('/dashboard/roles returns 403 (PROTECTED — has middleware)', async ({ page }) => {
        const response = await page.goto('/dashboard/roles');
        expect(response?.status()).toBe(403);
    });

    test('/dashboard/accounts returns 403 (PROTECTED)', async ({ page }) => {
        const response = await page.goto('/dashboard/accounts');
        expect(response?.status()).toBe(403);
    });

    test('/dashboard/users returns 403 (PROTECTED)', async ({ page }) => {
        const response = await page.goto('/dashboard/users');
        expect(response?.status()).toBe(403);
    });

    test('/dashboard/invoices returns 403 (PROTECTED)', async ({ page }) => {
        const response = await page.goto('/dashboard/invoices');
        expect(response?.status()).toBe(403);
    });
});

// ─────────────────────────────────────────────────────────────
// Role Management CRUD (Super Admin)
// ─────────────────────────────────────────────────────────────
test.describe('Role Management CRUD', () => {
    const TEST_ROLE_NAME = `E2E Test Role ${Date.now()}`;

    test.beforeEach(async ({ page }) => {
        await loginAs(page, SUPER_ADMIN.email, SUPER_ADMIN.password);
    });

    test('can create a new role with specific permissions', async ({ page }) => {
        await page.goto('/dashboard/roles/create');
        await page.waitForLoadState('networkidle');

        // Verify page title
        await expect(page.getByText('Create New Role')).toBeVisible();

        // Fill in role details (slug auto-generated from name, so leave it blank)
        await page.fill('input[placeholder="e.g., Editor, Accountant"]', TEST_ROLE_NAME);
        await page.fill('textarea[placeholder="Describe what this role is for..."]', 'Created by E2E test');

        // Select the Expenses module's permissions via the module-level checkbox
        const expensesCard = page.locator('.ant-card').filter({ hasText: 'Expenses' }).first();
        await expensesCard.locator('.ant-checkbox-input').first().check();

        // Verify permission count updated (should be > 0)
        const countText = await page.getByText(/Selected: \d+ permissions/).textContent();
        const count = parseInt(countText?.match(/\d+/)?.[0] || '0');
        expect(count).toBeGreaterThan(0);

        // Submit the form
        await page.click('button:has-text("Create Role")');

        // Wait for redirect to roles list
        await page.waitForURL('/dashboard/roles', { timeout: 15000 });
        await page.waitForLoadState('networkidle');

        // Verify role appears in the list
        await expect(page.getByText(TEST_ROLE_NAME)).toBeVisible();
    });

    test('can navigate to edit page from roles list', async ({ page }) => {
        // First create a role to edit
        await page.goto('/dashboard/roles/create');
        await page.waitForLoadState('networkidle');

        const editRoleName = `Edit Test ${Date.now()}`;
        await page.fill('input[placeholder="e.g., Editor, Accountant"]', editRoleName);

        // Select Expenses module
        const expensesCard = page.locator('.ant-card').filter({ hasText: 'Expenses' }).first();
        await expensesCard.locator('.ant-checkbox').first().click();

        await page.click('button:has-text("Create Role")');
        await page.waitForURL('/dashboard/roles', { timeout: 10000 });
        await page.waitForLoadState('networkidle');

        // Find the role row and click its actions dropdown
        const roleRow = page.locator('tr').filter({ hasText: editRoleName });
        await roleRow.locator('.ant-dropdown-trigger').click();

        // Click Edit link in the dropdown
        const editLink = page.locator('.ant-dropdown').getByText('Edit', { exact: true });
        await editLink.click();

        // Verify navigation to the edit page
        await page.waitForURL(/\/dashboard\/roles\/\d+\/edit/, { timeout: 10000 });
        await page.waitForLoadState('networkidle');

        // The edit page loads — verify the form and Update button are present
        await expect(page.getByText('Update Role')).toBeVisible();
        await expect(page.getByRole('heading', { name: 'Permissions' })).toBeVisible();
    });

    test('can delete a role via actions menu', async ({ page }) => {
        // Create a role to delete
        await page.goto('/dashboard/roles/create');
        await page.waitForLoadState('networkidle');

        const deleteRoleName = `Del Role ${Date.now()}`;
        await page.fill('input[placeholder="e.g., Editor, Accountant"]', deleteRoleName);

        // Select one permission
        const expensesCard = page.locator('.ant-card').filter({ hasText: 'Expenses' }).first();
        await expensesCard.locator('.ant-checkbox').first().click();

        await page.click('button:has-text("Create Role")');
        await page.waitForURL('/dashboard/roles', { timeout: 10000 });
        await page.waitForLoadState('networkidle');

        // Verify it exists
        await expect(page.getByText(deleteRoleName).first()).toBeVisible();

        // Open actions menu for this role
        const roleRow = page.locator('tr').filter({ hasText: deleteRoleName });
        await roleRow.locator('.ant-dropdown-trigger').click();

        // Click Delete from the dropdown
        const deleteLink = page.locator('.ant-dropdown').getByText('Delete', { exact: true });
        await deleteLink.click();

        // Confirm deletion in the modal
        await page.locator('.ant-modal-confirm-btns').getByRole('button', { name: 'Delete' }).click();

        // Wait for the Inertia redirect and page to settle after delete
        await page.waitForLoadState('networkidle');

        // Reload the page to get fresh data from the server
        await page.goto('/dashboard/roles');
        await page.waitForLoadState('networkidle');

        // Verify role is removed from the list
        await expect(page.getByText(deleteRoleName, { exact: true })).not.toBeVisible();
    });

    test('Select All / Clear All buttons work', async ({ page }) => {
        await page.goto('/dashboard/roles/create');
        await page.waitForLoadState('networkidle');

        // Click "Select All"
        await page.click('button:has-text("Select All")');

        // Verify the count shows all permissions selected (should be > 0)
        const selectedText = await page.getByText(/Selected: \d+ permissions/).textContent();
        const allCount = parseInt(selectedText?.match(/\d+/)?.[0] || '0');
        expect(allCount).toBeGreaterThan(0);

        // Click "Clear All"
        await page.click('button:has-text("Clear All")');

        // Verify count is 0
        await expect(page.getByText('Selected: 0 permissions')).toBeVisible();
    });

    test('module-level checkbox toggles all permissions in that module', async ({ page }) => {
        await page.goto('/dashboard/roles/create');
        await page.waitForLoadState('networkidle');

        // Start with 0 selected
        await expect(page.getByText('Selected: 0 permissions')).toBeVisible();

        // Click the module-level checkbox for Accounts
        const accountsCard = page.locator('.ant-card').filter({ hasText: 'Accounts' }).first();
        await accountsCard.locator('.ant-checkbox').first().click();

        // Count should now be > 0
        const selectedText = await page.getByText(/Selected: \d+ permissions/).textContent();
        const count = parseInt(selectedText?.match(/\d+/)?.[0] || '0');
        expect(count).toBeGreaterThan(0);

        // Click it again to deselect all in module
        await accountsCard.locator('.ant-checkbox').first().click();

        // Count should be back to 0
        await expect(page.getByText('Selected: 0 permissions')).toBeVisible();
    });
});
