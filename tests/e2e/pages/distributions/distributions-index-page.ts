import { Page } from '@playwright/test';

export class DistributionsIndexPage {
    readonly page: Page;
    readonly url = '/dashboard/distributions';

    constructor(page: Page) {
        this.page = page;
    }

    async navigate(): Promise<void> {
        await this.page.goto(this.url);
        await this.waitForTableLoad();
    }

    async waitForTableLoad(): Promise<void> {
        await this.page
            .waitForSelector('.ant-spin-spinning', { state: 'detached', timeout: 5000 })
            .catch(() => {});
    }

    async clickNewDistribution(): Promise<void> {
        await this.page.click('button:has-text("New Distribution")');
        await this.page.waitForSelector('.ant-modal', { state: 'visible' });
    }

    async createDistribution(periodStart: string, periodEnd: string, notes?: string): Promise<void> {
        await this.clickNewDistribution();

        // Click date range picker
        await this.page.click('.ant-picker-range');

        // Select start date
        await this.page.click(`td[title="${periodStart}"]`);

        // Select end date
        await this.page.click(`td[title="${periodEnd}"]`);

        // Fill notes if provided
        if (notes) {
            await this.page.fill('textarea', notes);
        }

        // Submit
        await this.page.click('.ant-modal-footer button.ant-btn-primary');

        // Wait for redirect to show page
        await this.page.waitForURL(/\/dashboard\/distributions\/\d+/, { timeout: 10000 });
    }

    async viewDistribution(distributionNumber: string): Promise<void> {
        const row = this.page.locator(`tr:has-text("${distributionNumber}")`);
        await row.locator('button:has-text("View")').click();
        await this.page.waitForURL(/\/dashboard\/distributions\/\d+/);
    }

    async deleteDistribution(distributionNumber: string): Promise<void> {
        const row = this.page.locator(`tr:has-text("${distributionNumber}")`);
        await row.locator('button:has-text("Delete")').click();
        await this.page.click('.ant-popconfirm button:has-text("Yes")');
        await this.waitForNotification('success');
    }

    async waitForNotification(type: 'success' | 'error'): Promise<void> {
        const notification = this.page.locator(`.ant-notification-notice-${type}`);
        await notification.waitFor({ state: 'visible', timeout: 5000 });
    }

    async getTableRowCount(): Promise<number> {
        await this.waitForTableLoad();
        const rows = await this.page.locator('.ant-table-tbody tr').count();
        return rows;
    }
}
