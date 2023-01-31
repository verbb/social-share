<?php
namespace verbb\socialshare\assetbundles;

use craft\web\AssetBundle;

class FrontEndAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = "@verbb/socialshare/resources/dist";

        $this->css = [
            'css/social-buttons.css',
        ];

        parent::init();
    }
}
