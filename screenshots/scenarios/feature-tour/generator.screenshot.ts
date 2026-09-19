import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSocialShareFixture } from '../../support/fixtures';

export default defineScreenshotScenario({
    id: 'social-share-feature-tour-generator',
    output: 'feature-tour/social-share-generator.png',
    route: '/social-share-generator-screenshot.html',
    viewport: { width: 650, height: 240, deviceScaleFactor: 2 },
    expectedOutput: { width: 1100, height: 278 },
    setup: seedSocialShareFixture,
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '#social-share-generator-frame', state: 'visible' },
    ],
    target: { type: 'selector', selector: '#social-share-generator-frame', padding: 0 },
    caption: 'Social Share buttons using several built-in presentation options.',
    intent: 'Recreates the production themed-button role with current provider icons and brand metadata.',
});
