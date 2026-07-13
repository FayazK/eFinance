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
        await this.page.getByRole('button', { name: 'New Distribution' }).click();
        await this.page.waitForURL(/\/dashboard\/distributions\/create$/, { timeout: 10000 });
    }

    /**
     * Create a draft distribution via the full-page create form and land on its show page.
     *
     * Creation is a full page now (not a modal): select the first PKR account, enter a manual
     * amount, optionally add notes, then "Save as Draft". The app redirects to the index, so we
     * read the new distribution's id from the store response and navigate to its show page.
     */
    async createDistribution(amount: number, notes?: string): Promise<void> {
        await this.clickNewDistribution();

        // Select the first available PKR bank account
        const accountItem = this.page
            .locator('.ant-form-item')
            .filter({ has: this.page.locator('label', { hasText: 'Bank Account' }) });
        await accountItem.locator('.ant-select-selector').click();
        await this.page
            .locator('.ant-select-dropdown:not(.ant-select-dropdown-hidden) .ant-select-item-option')
            .first()
            .click();

        // Enter the manual distribution amount (PKR, major units)
        await this.page.getByPlaceholder('Enter amount in PKR').fill(amount.toString());

        // Fill notes if provided
        if (notes) {
            await this.page.getByPlaceholder('Optional notes...').fill(notes);
        }

        // Save as draft, capturing the created distribution's id from the store response
        const [response] = await Promise.all([
            this.page.waitForResponse(
                (r) => new URL(r.url()).pathname === '/dashboard/distributions' && r.request().method() === 'POST',
            ),
            this.page.getByRole('button', { name: 'Save as Draft' }).click(),
        ]);
        const id = (await response.json())?.data?.id;

        // The create flow redirects to the index; navigate to the new distribution's show page
        await this.page.goto(`/dashboard/distributions/${id}`);
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
