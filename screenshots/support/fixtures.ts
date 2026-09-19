import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

import type { ScreenshotSetupContext } from '@verbb/craft-screenshots/types';

const supportDir = dirname(fileURLToPath(import.meta.url));
const seedScript = readFileSync(join(supportDir, 'seed', 'seed-social-share.php'), 'utf8');

/** Build deterministic public examples from Social Share's live provider catalogue. */
export async function seedSocialShareFixture(context: ScreenshotSetupContext): Promise<void> {
    const output = await context.runCraftScript(seedScript, { label: 'seed-social-share' });
    const fixture = JSON.parse(output.trim()) as { providerCount?: number };

    if (!fixture.providerCount) {
        throw new Error(`Social Share providers were not rendered: ${output}`);
    }
}
