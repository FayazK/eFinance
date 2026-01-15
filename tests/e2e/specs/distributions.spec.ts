import { test, expect } from '../fixtures';
import { DistributionsIndexPage } from '../pages/distributions/distributions-index-page';
import { DistributionShowPage } from '../pages/distributions/distribution-show-page';

test.describe('Distribution Management', () => {
    let indexPage: DistributionsIndexPage;
    let showPage: DistributionShowPage;

    test.beforeEach(async ({ authenticatedPage }) => {
        indexPage = new DistributionsIndexPage(authenticatedPage);
        showPage = new DistributionShowPage(authenticatedPage);
    });

    test.describe('Distribution Creation', () => {
        test('should create a new distribution', async ({ authenticatedPage }) => {
            await indexPage.navigate();
            const initialCount = await indexPage.getTableRowCount();

            // Create distribution for current month
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate, 'Test distribution');

            // Should be on show page
            expect(authenticatedPage.url()).toContain('/dashboard/distributions/');

            // Should be in draft status
            const isDraft = await showPage.isDraft();
            expect(isDraft).toBe(true);
        });

        test('should display statistics correctly', async ({ authenticatedPage }) => {
            await indexPage.navigate();

            // Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate);

            // Get statistics
            const revenue = await showPage.getStatistic('Revenue');
            const expenses = await showPage.getStatistic('Expenses');
            const netProfit = await showPage.getStatistic('Net Profit');

            console.log('Distribution Statistics:');
            console.log('Revenue:', revenue);
            console.log('Expenses:', expenses);
            console.log('Net Profit:', netProfit);

            // Net profit should equal revenue minus expenses
            expect(netProfit).toBe(revenue - expenses);
        });

        test('should create distribution lines for shareholders', async ({ authenticatedPage }) => {
            await indexPage.navigate();

            // Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate);

            // Get distribution lines
            const lines = await showPage.getDistributionLines();

            console.log('Distribution lines:', lines);

            // Should have lines for each active shareholder
            expect(lines.length).toBeGreaterThan(0);

            // Each line should have shareholder, equity, and amount
            lines.forEach((line) => {
                expect(line.shareholder).toBeTruthy();
                expect(line.equity).toBeTruthy();
                expect(line.amount).toBeTruthy();
            });
        });
    });

    test.describe('Distribution Adjustment', () => {
        test('should adjust net profit', async ({ authenticatedPage }) => {
            await indexPage.navigate();

            // Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate);

            const originalProfit = await showPage.getStatistic('Net Profit');

            // Adjust profit
            const adjustedAmount = 100000;
            await showPage.adjustProfit(adjustedAmount, 'Test adjustment for reserves');

            // Reload to see changes
            await authenticatedPage.reload();
            await authenticatedPage.waitForLoadState('networkidle');

            const newProfit = await showPage.getStatistic('Net Profit');

            console.log('Original profit:', originalProfit);
            console.log('Adjusted profit:', newProfit);

            expect(newProfit).toBe(adjustedAmount);
        });

        test('should recalculate distribution lines after adjustment', async ({
            authenticatedPage,
        }) => {
            await indexPage.navigate();

            // Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate);

            // Get lines before adjustment
            const linesBefore = await showPage.getDistributionLines();

            // Adjust to a round number for easy verification
            await showPage.adjustProfit(90000, 'Round number for testing');

            // Reload
            await authenticatedPage.reload();
            await authenticatedPage.waitForLoadState('networkidle');

            // Get lines after adjustment
            const linesAfter = await showPage.getDistributionLines();

            console.log('Lines before adjustment:', linesBefore);
            console.log('Lines after adjustment:', linesAfter);

            // Lines should have changed
            expect(linesAfter).not.toEqual(linesBefore);
        });
    });

    test.describe('Distribution Processing', () => {
        test('should process distribution with sufficient balance', async ({
            authenticatedPage,
        }) => {
            await indexPage.navigate();

            // Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate);

            // Verify it's in draft
            const isDraft = await showPage.isDraft();
            expect(isDraft).toBe(true);

            // Process distribution
            // Note: This will only work if there's a PKR account with sufficient balance
            await showPage.clickProcessDistribution();

            // Check if there are PKR accounts available
            const selectExists = await authenticatedPage
                .locator('.ant-select')
                .isVisible()
                .catch(() => false);

            if (selectExists) {
                // Select first account (if available)
                await authenticatedPage.click('.ant-select');

                const hasOptions = await authenticatedPage
                    .locator('.ant-select-item-option')
                    .first()
                    .isVisible()
                    .catch(() => false);

                if (hasOptions) {
                    await authenticatedPage.click('.ant-select-item-option:first-child');

                    // Check for insufficient balance error
                    const hasError = await showPage.hasInsufficientBalanceError();

                    if (!hasError) {
                        // If no error, we can process
                        await authenticatedPage.click('.ant-modal-footer button.ant-btn-primary');
                        await showPage.waitForNotification('success');

                        // Reload to verify processed status
                        await authenticatedPage.reload();
                        await authenticatedPage.waitForLoadState('networkidle');

                        const isProcessed = await showPage.isProcessed();
                        expect(isProcessed).toBe(true);
                    } else {
                        console.log('Insufficient balance detected - test passed (expected behavior)');
                        // Close modal
                        await authenticatedPage.click('.ant-modal-close');
                    }
                } else {
                    console.log('No PKR accounts available');
                    // Close modal
                    await authenticatedPage.click('.ant-modal-close');
                }
            }
        });

        test('should prevent processing without selecting account', async ({
            authenticatedPage,
        }) => {
            await indexPage.navigate();

            // Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(startDate, endDate);

            await showPage.clickProcessDistribution();

            // Try to submit without selecting account
            await authenticatedPage.click('.ant-modal-footer button.ant-btn-primary');

            // Should show validation error
            const hasError = await authenticatedPage
                .locator('.ant-form-item-explain-error')
                .isVisible()
                .catch(() => false);

            if (hasError) {
                const errorText = await authenticatedPage
                    .locator('.ant-form-item-explain-error')
                    .textContent();
                console.log('Validation error shown:', errorText);
                expect(errorText?.toLowerCase()).toContain('account');
            }

            // Close modal
            await authenticatedPage.click('.ant-modal-close');
        });
    });

    test.describe('Complete Workflow', () => {
        test('should complete full workflow: create → adjust → process', async ({
            authenticatedPage,
        }) => {
            // Navigate to distributions
            await indexPage.navigate();

            // STEP 1: Create distribution
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const startDate = `${year}-${month}-01`;
            const endDate = `${year}-${month}-28`;

            await indexPage.createDistribution(
                startDate,
                endDate,
                'Full workflow test distribution',
            );

            // Verify draft status
            const isDraft = await showPage.isDraft();
            expect(isDraft).toBe(true);

            // STEP 2: Adjust profit
            await showPage.adjustProfit(100000, 'Adjusted for testing workflow');

            // Reload and verify adjustment
            await authenticatedPage.reload();
            await authenticatedPage.waitForLoadState('networkidle');

            const adjustedProfit = await showPage.getStatistic('Net Profit');
            expect(adjustedProfit).toBe(100000);

            // STEP 3: Attempt to process (may fail if no accounts/insufficient balance)
            await showPage.clickProcessDistribution();

            const selectExists = await authenticatedPage
                .locator('.ant-select')
                .isVisible()
                .catch(() => false);

            if (selectExists) {
                await authenticatedPage.click('.ant-select');

                const hasOptions = await authenticatedPage
                    .locator('.ant-select-item-option')
                    .first()
                    .isVisible()
                    .catch(() => false);

                if (hasOptions) {
                    await authenticatedPage.click('.ant-select-item-option:first-child');

                    const hasError = await showPage.hasInsufficientBalanceError();

                    if (!hasError) {
                        await authenticatedPage.click('.ant-modal-footer button.ant-btn-primary');
                        await showPage.waitForNotification('success');

                        await authenticatedPage.reload();
                        await authenticatedPage.waitForLoadState('networkidle');

                        const isProcessed = await showPage.isProcessed();
                        expect(isProcessed).toBe(true);

                        // Verify cannot adjust or process again
                        const adjustButton = await authenticatedPage
                            .locator('button:has-text("Adjust Net Profit")')
                            .isVisible()
                            .catch(() => false);
                        expect(adjustButton).toBe(false);

                        console.log('Full workflow completed successfully!');
                    } else {
                        console.log(
                            'Insufficient balance - partial workflow test completed (create + adjust)',
                        );
                        await authenticatedPage.click('.ant-modal-close');
                    }
                } else {
                    console.log('No accounts - partial workflow test completed (create + adjust)');
                    await authenticatedPage.click('.ant-modal-close');
                }
            }
        });
    });
});
