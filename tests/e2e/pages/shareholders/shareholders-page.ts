import { Page, Locator } from '@playwright/test';

export class ShareholdersPage {
    readonly page: Page;
    readonly url = '/dashboard/shareholders';

    constructor(page: Page) {
        this.page = page;
    }

    async navigate(): Promise<void> {
        await this.page.goto(this.url);
        await this.waitForTableLoad();
    }

    async waitForTableLoad(): Promise<void> {
        // Wait for Ant Design table loading to finish
        await this.page
            .waitForSelector('.ant-spin-spinning', { state: 'detached', timeout: 5000 })
            .catch(() => {});
    }

    async clickCreateButton(): Promise<void> {
        await this.page.click('button:has-text("Create Shareholder")');
        await this.page.waitForSelector('.ant-modal', { state: 'visible' });
    }

    async fillForm(data: {
        name?: string;
        email?: string;
        equity_percentage?: number;
        is_office_reserve?: boolean;
        is_active?: boolean;
        notes?: string;
    }): Promise<void> {
        if (data.name !== undefined) {
            await this.page.fill('input[placeholder="John Doe"]', data.name);
        }

        if (data.email !== undefined) {
            await this.page.fill('input[type="email"]', data.email);
        }

        if (data.equity_percentage !== undefined) {
            // Clear the input first
            await this.page.fill('input[type="number"]', '');
            await this.page.fill('input[type="number"]', data.equity_percentage.toString());
        }

        if (data.is_office_reserve !== undefined) {
            const checkbox = this.page.locator('input[type="checkbox"]').first();
            const isChecked = await checkbox.isChecked();
            if (data.is_office_reserve !== isChecked) {
                await checkbox.click();
            }
        }

        if (data.is_active !== undefined) {
            const checkbox = this.page.locator('input[type="checkbox"]').nth(1);
            const isChecked = await checkbox.isChecked();
            if (data.is_active !== isChecked) {
                await checkbox.click();
            }
        }

        if (data.notes !== undefined) {
            await this.page.fill('textarea', data.notes);
        }
    }

    async submitForm(): Promise<void> {
        await this.page.click('.ant-modal-footer button.ant-btn-primary');
        await this.waitForNotification('success');
    }

    async waitForNotification(type: 'success' | 'error'): Promise<void> {
        const notification = this.page.locator(`.ant-notification-notice-${type}`);
        await notification.waitFor({ state: 'visible', timeout: 5000 });
    }

    async editShareholder(name: string): Promise<void> {
        const row = this.page.locator(`tr:has-text("${name}")`);
        await row.locator('button:has-text("Edit")').click();
        await this.page.waitForSelector('.ant-modal', { state: 'visible' });
    }

    async deleteShareholder(name: string): Promise<void> {
        const row = this.page.locator(`tr:has-text("${name}")`);
        await row.locator('button:has-text("Delete")').click();
        // Wait for popconfirm
        await this.page.click('.ant-popconfirm button:has-text("Yes")');
        await this.waitForNotification('success');
    }

    async getEquityValidation(): Promise<{
        isValid: boolean;
        message: string;
    }> {
        const alert = this.page.locator('.ant-alert').first();
        const message = (await alert.textContent()) || '';

        const isSuccess = await this.page
            .locator('.ant-alert-success')
            .first()
            .isVisible()
            .catch(() => false);

        return {
            isValid: isSuccess,
            message,
        };
    }

    async getTableRowCount(): Promise<number> {
        await this.waitForTableLoad();
        const rows = await this.page.locator('.ant-table-tbody tr').count();
        return rows;
    }

    async getShareholderByName(name: string): Promise<{
        name: string;
        email: string;
        equity: string;
        type: string;
        status: string;
    } | null> {
        const row = this.page.locator(`tr:has-text("${name}")`);
        const isVisible = await row.isVisible().catch(() => false);

        if (!isVisible) return null;

        return {
            name,
            email: (await row.locator('td').nth(1).textContent()) || '',
            equity: (await row.locator('td').nth(2).textContent()) || '',
            type: (await row.locator('td').nth(3).textContent()) || '',
            status: (await row.locator('td').nth(4).textContent()) || '',
        };
    }

    async closeModal(): Promise<void> {
        await this.page.click('.ant-modal-footer button:not(.ant-btn-primary)');
        await this.page.waitForSelector('.ant-modal', { state: 'hidden' });
    }
}
