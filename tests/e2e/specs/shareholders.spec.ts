import { test, expect } from '../fixtures';
import { ShareholdersPage } from '../pages/shareholders/shareholders-page';

test.describe('Shareholder Management', () => {
    let shareholdersPage: ShareholdersPage;

    test.beforeEach(async ({ authenticatedPage }) => {
        shareholdersPage = new ShareholdersPage(authenticatedPage);
        await shareholdersPage.navigate();
    });

    test.describe('Shareholder CRUD Operations', () => {
        test('should create a new shareholder successfully', async ({ authenticatedPage }) => {
            const initialCount = await shareholdersPage.getTableRowCount();

            await shareholdersPage.clickCreateButton();
            await shareholdersPage.fillForm({
                name: 'Test Partner',
                email: 'test@partner.com',
                equity_percentage: 10,
                is_office_reserve: false,
                is_active: true,
                notes: 'Test shareholder',
            });
            await shareholdersPage.submitForm();

            // Verify shareholder appears in table
            await shareholdersPage.navigate();
            const newCount = await shareholdersPage.getTableRowCount();
            expect(newCount).toBe(initialCount + 1);

            const shareholder = await shareholdersPage.getShareholderByName('Test Partner');
            expect(shareholder).not.toBeNull();
            expect(shareholder?.email).toContain('test@partner.com');
            expect(shareholder?.equity).toContain('10');
        });

        test('should edit an existing shareholder', async ({ authenticatedPage }) => {
            // First create a shareholder
            await shareholdersPage.clickCreateButton();
            await shareholdersPage.fillForm({
                name: 'Edit Test Partner',
                equity_percentage: 15,
                is_active: true,
            });
            await shareholdersPage.submitForm();

            // Reload page
            await shareholdersPage.navigate();

            // Edit the shareholder
            await shareholdersPage.editShareholder('Edit Test Partner');
            await shareholdersPage.fillForm({
                equity_percentage: 20,
            });
            await shareholdersPage.submitForm();

            // Verify changes
            await shareholdersPage.navigate();
            const shareholder = await shareholdersPage.getShareholderByName('Edit Test Partner');
            expect(shareholder?.equity).toContain('20');
        });

        test('should delete a shareholder', async ({ authenticatedPage }) => {
            // First create a shareholder
            await shareholdersPage.clickCreateButton();
            await shareholdersPage.fillForm({
                name: 'Delete Test Partner',
                equity_percentage: 5,
                is_active: true,
            });
            await shareholdersPage.submitForm();

            await shareholdersPage.navigate();
            const initialCount = await shareholdersPage.getTableRowCount();

            // Delete the shareholder
            await shareholdersPage.deleteShareholder('Delete Test Partner');

            await shareholdersPage.navigate();
            const newCount = await shareholdersPage.getTableRowCount();
            expect(newCount).toBe(initialCount - 1);
        });
    });

    test.describe('Equity Validation', () => {
        test('should show remaining equity when creating shareholder', async ({
            authenticatedPage,
        }) => {
            await shareholdersPage.clickCreateButton();

            // Get current equity validation
            const validation = await shareholdersPage.getEquityValidation();
            console.log('Current equity validation:', validation);

            // The validation message should show total equity percentage
            expect(validation.message).toBeTruthy();
        });

        test('should validate equity does not exceed 100%', async ({ authenticatedPage }) => {
            // Try to create shareholder with high equity when total is already at or near 100%
            const validation = await shareholdersPage.getEquityValidation();

            if (validation.isValid) {
                // If equity is already at 100%, trying to add more should fail
                await shareholdersPage.clickCreateButton();
                await shareholdersPage.fillForm({
                    name: 'Excess Partner',
                    equity_percentage: 10,
                });
                await shareholdersPage.submitForm();

                // Should show error notification
                await expect(
                    authenticatedPage.locator('.ant-notification-notice-error'),
                ).toBeVisible();
                await shareholdersPage.closeModal();
            }
        });

        test('should show equity validation status', async ({ authenticatedPage }) => {
            const validation = await shareholdersPage.getEquityValidation();

            // Should have a validation message
            expect(validation.message).toBeTruthy();

            // Should show either valid or invalid state
            console.log('Equity is valid:', validation.isValid);
            console.log('Validation message:', validation.message);
        });
    });

    test.describe('Form Validation', () => {
        test('should require name field', async ({ authenticatedPage }) => {
            await shareholdersPage.clickCreateButton();

            // Submit without filling name
            await shareholdersPage.fillForm({
                equity_percentage: 10,
            });

            // Click submit
            await authenticatedPage.click('.ant-modal-footer button.ant-btn-primary');

            // Should show validation error
            await expect(authenticatedPage.locator('.ant-form-item-explain-error')).toBeVisible();
        });

        test('should validate email format', async ({ authenticatedPage }) => {
            await shareholdersPage.clickCreateButton();

            await shareholdersPage.fillForm({
                name: 'Test',
                email: 'invalid-email',
                equity_percentage: 10,
            });

            // Click submit
            await authenticatedPage.click('.ant-modal-footer button.ant-btn-primary');

            // Should show validation error for email
            const error = await authenticatedPage
                .locator('.ant-form-item-explain-error')
                .textContent();
            expect(error?.toLowerCase()).toContain('email');
        });
    });
});
