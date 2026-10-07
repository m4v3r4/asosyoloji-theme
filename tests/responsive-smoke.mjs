import { chromium } from 'playwright';
import AxeBuilder from '@axe-core/playwright';

const viewports = [
  { name: 'desktop', width: 1440, height: 1000 },
  { name: 'tablet', width: 768, height: 1024 },
  { name: 'mobile', width: 390, height: 844 }
];

const browser = await chromium.launch({ headless: true });
let failed = false;

for (const viewport of viewports) {
  const context = await browser.newContext({ viewport: { width: viewport.width, height: viewport.height } });
  const page = await context.newPage();

  await page.goto('http://127.0.0.1:8080/', { waitUntil: 'networkidle' });

  const result = await page.evaluate(() => ({
    width: window.innerWidth,
    scrollWidth: document.documentElement.scrollWidth,
    header: !!document.querySelector('.site-header'),
    main: !!document.querySelector('#primary'),
    footer: !!document.querySelector('.site-footer')
  }));

  if (!result.header || !result.main || !result.footer) {
    console.error(`${viewport.name}: required theme landmarks missing`, result);
    failed = true;
  }

  if (result.scrollWidth > result.width + 2) {
    console.error(`${viewport.name}: horizontal overflow ${result.scrollWidth}px > ${result.width}px`);
    failed = true;
  }

  const accessibility = await new AxeBuilder({ page })
    .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
    .analyze();

  const blockingViolations = accessibility.violations.filter((violation) =>
    ['serious', 'critical'].includes(violation.impact)
  );

  if (blockingViolations.length) {
    console.error(
      `${viewport.name}: accessibility violations`,
      blockingViolations.map((violation) => ({
        id: violation.id,
        impact: violation.impact,
        description: violation.description,
        nodes: violation.nodes.length
      }))
    );
    failed = true;
  }

  const toggle = page.locator('.theme-toggle');
  if (await toggle.count()) {
    await toggle.click();
    const theme = await page.evaluate(() => document.documentElement.dataset.theme);
    if (theme !== 'dark') {
      console.error(`${viewport.name}: theme toggle did not switch to dark mode`);
      failed = true;
    }
  }

  await context.close();
}

await browser.close();

if (failed) {
  process.exit(1);
}

console.log('Responsive and dark-mode browser smoke passed.');
