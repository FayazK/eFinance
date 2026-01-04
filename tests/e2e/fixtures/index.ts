import { test as base, Page } from '@playwright/test';
import { authenticate } from './auth';
import path from 'path';
import { fileURLToPath } from 'url';
import { existsSync, mkdirSync } from 'fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const STORAGE_STATE = path.join(__dirname, '../../../.auth/user.json');

type TestFixtures = {
    authenticatedPage: Page;
};

export const test = base.extend<TestFixtures>({
    authenticatedPage: async ({ browser }, use) => {
        // Ensure .auth directory exists
        const authDir = path.join(__dirname, '../../../.auth');
        if (!existsSync(authDir)) {
            mkdirSync(authDir, { recursive: true });
        }

        // Create context with or without stored auth
        let context;
        if (existsSync(STORAGE_STATE)) {
            context = await browser.newContext({ storageState: STORAGE_STATE });
        } else {
            context = await browser.newContext();
        }

        const page = await context.newPage();

        // If no stored auth exists, authenticate now
        if (!existsSync(STORAGE_STATE)) {
            await authenticate(page);
        }

        // Navigate to dashboard to ensure we're logged in
        await page.goto('/dashboard');

        await use(page);

        await context.close();
    },
});

export { expect } from '@playwright/test';
