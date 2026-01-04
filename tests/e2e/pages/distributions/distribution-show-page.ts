import { Page } from '@playwright/test';

export class DistributionShowPage {
    readonly page: Page;

    constructor(page: Page) {
        this.page = page;
    }

    async getStatistic(title: string): Promise<number> {
        const statistic = this.page.locator(
            `.ant-statistic:has(.ant-statistic-title:has-text("${title}"))`,
        );
        const valueText = await statistic.locator('.ant-statistic-content-value').textContent();

        // Remove "Rs" prefix and commas, then parse
        return parseFloat(valueText?.replace(/Rs\s*/, '').replace(/,/g, '') || '0');
    }

    async isDraft(): Promise<boolean> {
        // Check both alert and status tag
        const hasAlert = await this.page
            .locator('.ant-alert-info:has-text("Draft Distribution")')
            .isVisible()
            .catch(() => false);

        const hasTag = await this.page
            .locator('text=Status').locator('..').locator('.ant-tag:has-text("Draft")')
            .isVisible()
            .catch(() => false);

        return hasAlert || hasTag;
    }

    async isProcessed(): Promise<boolean> {
        // Check both alert and status tag
        const hasAlert = await this.page
            .locator('.ant-alert-success:has-text("Processed Distribution")')
            .isVisible()
            .catch(() => false);

        const hasTag = await this.page
            .locator('text=Status').locator('..').locator('.ant-tag:has-text("Processed")')
            .isVisible()
            .catch(() => false);

        return hasAlert || hasTag;
    }

    async clickAdjustNetProfit(): Promise<void> {
        await this.page.click('button:has-text("Adjust Net Profit")');
        await this.page.waitForSelector('.ant-modal', { state: 'visible' });
    }

    async adjustProfit(amount: number, reason: string): Promise<void> {
        await this.clickAdjustNetProfit();

        // Fill amount
        await this.page.fill('input[type="number"]', amount.toString());

        // Fill reason
        await this.page.fill('textarea', reason);

        // Submit
        await this.page.click('.ant-modal-footer button.ant-btn-primary');
        await this.waitForNotification('success');
    }

    async clickProcessDistribution(): Promise<void> {
        await this.page.click('button:has-text("Process Distribution")');
        await this.page.waitForSelector('.ant-modal', { state: 'visible' });
    }

    async processDistribution(accountName: string): Promise<void> {
        await this.clickProcessDistribution();

        // Select account from dropdown
        await this.page.click('.ant-select');
        await this.page.click(`[title*="${accountName}"]`);

        // Wait a moment for balance calculations
        await this.page.waitForTimeout(500);

        // Submit
        await this.page.click('.ant-modal-footer button.ant-btn-primary');
        await this.waitForNotification('success');
    }

    async hasInsufficientBalanceError(): Promise<boolean> {
        return await this.page
            .locator('.ant-alert-error:has-text("Insufficient Balance")')
            .isVisible()
            .catch(() => false);
    }

    async getDistributionLines(): Promise<
        Array<{
            shareholder: string;
            equity: string;
            amount: string;
        }>
    > {
        await this.waitForTableLoad();
        const rows = this.page.locator('.ant-table-tbody tr');
        const count = await rows.count();

        const lines = [];
        for (let i = 0; i < count; i++) {
            const row = rows.nth(i);
            lines.push({
                shareholder: (await row.locator('td').nth(0).textContent()) || '',
                equity: (await row.locator('td').nth(1).textContent()) || '',
                amount: (await row.locator('td').nth(2).textContent()) || '',
            });
        }
        return lines;
    }

    async waitForTableLoad(): Promise<void> {
        await this.page
            .waitForSelector('.ant-spin-spinning', { state: 'detached', timeout: 5000 })
            .catch(() => {});
    }

    async waitForNotification(type: 'success' | 'error'): Promise<void> {
        const notification = this.page.locator(`.ant-notification-notice-${type}`);
        await notification.waitFor({ state: 'visible', timeout: 5000 });
    }
}
