import { defineScreenshotScenario } from '@verbb/craft-screenshots/api';

import { seedSocialShareFixture } from '../../support/fixtures';

export default defineScreenshotScenario({
    id: 'social-share-feature-tour-buttons',
    output: 'feature-tour/social-share-buttons.png',
    route: '/social-share-buttons-screenshot.html',
    viewport: { width: 900, height: 300, deviceScaleFactor: 2 },
    expectedOutput: { width: 1650, height: 420 },
    setup: seedSocialShareFixture,
    waitFor: [
        { type: 'loadState', state: 'networkidle' },
        { type: 'selector', selector: '#social-share-buttons-frame', state: 'visible' },
    ],
    target: { type: 'selector', selector: '#social-share-buttons-frame', padding: 0 },
    caption: 'The Social Share provider catalogue rendered as compact icon buttons.',
    intent: 'Recreates the production provider-grid role with the current Craft 5 plugin catalogue and current provider artwork.',
});
