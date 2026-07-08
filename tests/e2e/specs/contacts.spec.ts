import { expect, test } from '../fixtures';

test.describe('Contact form validation', () => {
    // Regression for issue #7: a server 422 on an indexed Form.List field
    // (e.g. additional_phones.0) must attach to that specific input, not only
    // fire the top-level notification. Additional phones have no client-side
    // rule, so an over-long value reaches the server and returns a real 422.
    test('indexed 422 error attaches to the offending additional-phone input', async ({ authenticatedPage: page }) => {
        await page.goto('/dashboard/contacts/create');

        // Fill required fields so client-side validation passes and the form submits.
        await page.fill('#first_name', 'Indexed');
        await page.fill('#last_name', 'ErrorTest');
        await page.fill('#primary_email', `indexed.error.${Date.now()}@example.com`);

        // Select the first available client (required AdvancedSelect; options load on mount).
        const clientItem = page.locator('.ant-form-item').filter({ has: page.locator('label', { hasText: 'Client' }) });
        await clientItem.locator('.ant-select-selector').click();
        const firstClient = page.locator('.ant-select-dropdown:not(.ant-select-dropdown-hidden) .ant-select-item-option').first();
        await firstClient.waitFor({ state: 'visible' });
        await firstClient.click();

        // Add an additional phone longer than the server's max:50 rule.
        await page.getByRole('button', { name: 'Add Phone Number' }).click();
        await page.getByPlaceholder('+1234567890').fill('x'.repeat(60));

        // Submit -> server returns 422 with an error on additional_phones.0.
        await page.getByRole('button', { name: 'Create Contact' }).click();

        // The error must render inside THAT phone input's form item, not just the notification.
        const phoneItem = page.locator('.ant-form-item').filter({ has: page.getByPlaceholder('+1234567890') });
        await expect(phoneItem.locator('.ant-form-item-explain-error')).toContainText('50');
    });

    test('indexed 422 error attaches to the offending additional-email input', async ({ authenticatedPage: page }) => {
        await page.goto('/dashboard/contacts/create');

        await page.fill('#first_name', 'Indexed');
        await page.fill('#last_name', 'EmailTest');
        await page.fill('#primary_email', `indexed.email.${Date.now()}@example.com`);

        const clientItem = page.locator('.ant-form-item').filter({ has: page.locator('label', { hasText: 'Client' }) });
        await clientItem.locator('.ant-select-selector').click();
        const firstClient = page.locator('.ant-select-dropdown:not(.ant-select-dropdown-hidden) .ant-select-item-option').first();
        await firstClient.waitFor({ state: 'visible' });
        await firstClient.click();

        // Valid email format but longer than the server's max:255 rule, so it passes the
        // client-side `type: 'email'` check and returns a real 422 on additional_emails.0.
        await page.getByRole('button', { name: 'Add Email' }).click();
        await page.getByPlaceholder('email@example.com').fill(`${'a'.repeat(260)}@example.com`);

        await page.getByRole('button', { name: 'Create Contact' }).click();

        const emailItem = page.locator('.ant-form-item').filter({ has: page.getByPlaceholder('email@example.com') });
        await expect(emailItem.locator('.ant-form-item-explain-error')).toContainText('255');
    });
});
