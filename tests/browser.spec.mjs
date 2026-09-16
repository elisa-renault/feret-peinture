/**
 * Real browser checks to run against an installed local preview.
 * Not executed in the authoring session: its browser security policy blocked local pages.
 * Run: FP_BASE_URL=http://localhost:8080 npx playwright test tests/browser.spec.mjs
 * The preview must have seeded services and Mailpit configuration, without demo projects.
 */
import { test, expect } from '@playwright/test';

const baseURL = (process.env.FP_BASE_URL || 'http://localhost:8080').replace(/\/$/, '');
const widths = [320, 360, 390, 768, 1024, 1440];

async function checkStructure(page, width) {
  await expect(page.locator('main h1')).toHaveCount(1);
  const dimensions = await page.evaluate(() => ({
    viewport: window.innerWidth,
    document: document.documentElement.scrollWidth,
    body: document.body.scrollWidth,
  }));
  expect(dimensions.viewport).toBe(width);
  expect(dimensions.document, 'Document must not overflow horizontally').toBeLessThanOrEqual(width + 1);
  expect(dimensions.body, 'Body must not overflow horizontally').toBeLessThanOrEqual(width + 1);
  await expect(page.locator('title')).not.toHaveText('');
  await expect(page.locator('link[rel="canonical"]')).toHaveCount(1);
  expect(await page.locator('link[rel="canonical"]').getAttribute('href')).toContain(baseURL);
}

for (const width of widths) {
  test(`home → service → quote, keyboard and screenshots at ${width}px`, async ({ page }, testInfo) => {
    await page.setViewportSize({ width, height: 960 });
    const consoleErrors = [];
    page.on('pageerror', (error) => consoleErrors.push(error.message));
    page.on('console', (message) => { if (message.type() === 'error') consoleErrors.push(message.text()); });

    await page.goto(baseURL + '/', { waitUntil: 'networkidle' });
    await page.evaluate(() => document.fonts.ready);
    await checkStructure(page, width);
    await expect(page.locator('h1')).toHaveText('Peintre en bâtiment à Écouen.');
    // A fresh public checkout has no approved telephone yet.
    for (const link of await page.locator('a[href^="tel:"]').all()) {
      await expect(link).toBeVisible();
      await expect(link).toHaveAttribute('href', /^tel:\+33[0-9]{9}$/);
    }
    await expect(page.locator('.faq-grid h2')).toHaveText(/Quelques\s+réponses utiles\./);
    await page.screenshot({ path: testInfo.outputPath(`accueil-${width}.png`), fullPage: true });

    await page.keyboard.press('Tab');
    await expect(page.locator('.skip-link')).toBeFocused();
    await page.keyboard.press('Enter');
    await expect(page.locator('#contenu')).toBeFocused();

    if (width <= 1000) {
      const toggle = page.getByRole('button', { name: 'Menu' });
      const nav = page.getByRole('navigation', { name: 'Navigation principale' });
      await expect(nav).not.toBeVisible();
      await toggle.focus();
      await page.keyboard.press('Enter');
      await expect(toggle).toHaveAttribute('aria-expanded', 'true');
      await expect(nav).toBeVisible();
      await page.keyboard.press('Tab');
      await expect(nav.locator('a').first()).toBeFocused();
      await page.keyboard.press('Escape');
      await expect(nav).not.toBeVisible();
      await expect(toggle).toBeFocused();
    }

    const firstService = page.locator('.service-row').first();
    await expect(firstService).toBeVisible();
    const serviceURL = await firstService.getAttribute('href');
    await firstService.click();
    await page.waitForURL(serviceURL);
    await checkStructure(page, width);
    await expect(page.locator('.service-content h2')).toHaveCount(3);
    await page.screenshot({ path: testInfo.outputPath(`prestation-${width}.png`), fullPage: true });
    await page.locator('.project-aside .button').click();
    await page.waitForURL(/\/devis\/\?prestation=/);
    await checkStructure(page, width);
    const serviceSlug = new URL(page.url()).searchParams.get('prestation');
    await expect(page.locator('#quote-type')).toHaveValue(serviceSlug);
    await expect(page.locator('#quote-phone')).toHaveAttribute('aria-describedby', 'contact-hint');
    await expect(page.locator('#quote-email')).toHaveAttribute('aria-describedby', 'contact-hint');
    if (width <= 390) {
      await expect(page.locator('#quote-name')).toBeInViewport();
    }
    await expect(page.locator('[data-quote-form]')).toHaveCount(1);
    await expect(page.locator('input[type="file"]')).toHaveCount(0);
    await expect(page.locator('input[type="hidden"]').first()).not.toBeVisible();
    await page.locator('#quote-description').focus();
    await expect(page.locator('#quote-description')).toBeFocused();
    await expect(page.locator('#quote-description')).toBeInViewport();
    await page.screenshot({ path: testInfo.outputPath(`devis-${width}.png`), fullPage: true });
    expect(consoleErrors).toEqual([]);
  });
}

test('404 and manually constructed thank-you URL never report a successful submission', async ({ page }) => {
  const response = await page.goto(baseURL + '/page-qui-nexiste-pas/');
  expect(response.status()).toBe(404);
  await expect(page.locator('main h1')).toHaveCount(1);
  await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', /noindex/);
  await page.goto(baseURL + '/merci/?sent=1');
  await expect(page.locator('[data-form-success]')).toHaveCount(0);
  await expect(page.locator('main')).toContainText('Pour transmettre votre projet');
  await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', /noindex/);
});

test('only anonymous, allowlisted local event names are emitted', async ({ page }) => {
  await page.goto(baseURL + '/devis/');
  await page.evaluate(() => {
    window.fpTestEvents = [];
    window.addEventListener('fp:analytics', (event) => window.fpTestEvents.push(event.detail));
  });
  await page.locator('#quote-name').fill('Navigateur de test');
  await page.locator('#quote-town').fill('Écouen');
  const events = await page.evaluate(() => window.fpTestEvents);
  expect(events).toEqual([{ name: 'form_start' }]);
  expect(JSON.stringify(events)).not.toContain('Navigateur');
  expect(JSON.stringify(events)).not.toContain('Écouen');
});
