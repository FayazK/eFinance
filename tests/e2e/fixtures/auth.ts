import { test as base, Page } from '@playwright/test';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const STORAGE_STATE = path.join(__dirname, '../../../.auth/user.json');

export async function authenticate(page: Page): Promise<void> {
    await page.goto('/login');

    // Wait for the login form to load
    await page.waitForSelector('input[placeholder*="email"]', { timeout: 10000 });

    // Fill in email and password using placeholder selectors
    await page.fill('input[placeholder*="email"]', 'info@fayazk.com');
    await page.fill('input[placeholder*="password"]', '@Password1');

    // Click the sign in button
    await page.click('button:has-text("Sign in")');

    // Wait for successful login (dashboard redirect)
    await page.waitForURL('/dashboard', { timeout: 10000 });

    // Save authentication state
    await page.context().storageState({ path: STORAGE_STATE });
}
