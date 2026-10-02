<?php
it('installs the plugin into a clean Craft application', function() {
    expect(Craft::$app->getIsInstalled())->toBeTrue();
    expect(Craft::$app->getPlugins()->isPluginEnabled('social-share'))->toBeTrue();
});
