<?php
namespace verbb\socialshare\web\assets\frontend;

use craft\web\AssetBundle;

class FrontEndAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/socialshare/web/assets/frontend/dist';

        $this->css = [
            'social-buttons.css',
        ];

        parent::init();
    }
}
