<?php
namespace verbb\socialshare\web\assets\frontend;

use craft\web\AssetBundle;

class ShareButtonAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/socialshare/web/assets/frontend/dist';

        $this->js = [
            'share-buttons.js',
        ];

        parent::init();
    }
}
