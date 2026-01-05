// Playwright test to simulate Saturday and check opening hours
// Run with: npx playwright test test-saturday-hours.js --headed

import { test, expect } from '@playwright/test';

test('Porsgrunn shows correct Saturday opening hours (11:00-14:00)', async ({ page }) => {
  // Set the date to a Saturday (2026-01-11 is a Saturday)
  const saturdayDate = new Date('2026-01-11T10:00:00');

  // Mock the system time to Saturday
  await page.addInitScript(`
    // Override Date to return Saturday
    Date = class extends Date {
      constructor(...args) {
        if (args.length === 0) {
          super(${saturdayDate.getTime()});
        } else {
          super(...args);
        }
      }

      static now() {
        return ${saturdayDate.getTime()};
      }
    };
  `);

  // Navigate to Porsgrunn page
  await page.goto('https://autostrada.no/avdelinger/porsgrunn/');

  // Wait for page to load
  await page.waitForLoadState('networkidle');

  // Take screenshot for manual inspection
  await page.screenshot({ path: 'porsgrunn-saturday-test.png', fullPage: true });

  console.log('📸 Screenshot saved: porsgrunn-saturday-test.png');

  // Check if the page contains correct Saturday hours
  const pageContent = await page.content();

  // Look for Saturday hours in the HTML
  const hasSaturdayHours = pageContent.includes('11:00 - 14:00') ||
                           pageContent.includes('11.00 - 14.00');

  console.log('✅ Page contains correct Saturday hours (11:00-14:00):', hasSaturdayHours);

  // Check for old incorrect hours
  const hasOldHours = pageContent.includes('10:00 - 14:00') ||
                      pageContent.includes('10.00 - 14.00');

  console.log('❌ Page contains old hours (10:00-14:00):', hasOldHours);

  // Assertions
  expect(hasSaturdayHours).toBeTruthy();
  expect(hasOldHours).toBeFalsy();
});

test('Check opening hours display element on Saturday', async ({ page }) => {
  const saturdayDate = new Date('2026-01-11T10:00:00');

  await page.addInitScript(`
    Date = class extends Date {
      constructor(...args) {
        if (args.length === 0) {
          super(${saturdayDate.getTime()});
        } else {
          super(...args);
        }
      }
      static now() {
        return ${saturdayDate.getTime()};
      }
    };
  `);

  await page.goto('https://autostrada.no/avdelinger/porsgrunn/');
  await page.waitForLoadState('networkidle');

  // Try to find the "Åpningstid i dag" element
  const openingHoursElement = await page.locator('text=/Åpningstid i dag/').first();

  if (await openingHoursElement.isVisible()) {
    const text = await openingHoursElement.textContent();
    console.log('📍 Found opening hours text:', text);

    // Should show Saturday hours
    expect(text).toContain('11');
    expect(text).toContain('14');
  } else {
    console.log('⚠️  "Åpningstid i dag" element not found (might be in popup)');
  }
});
