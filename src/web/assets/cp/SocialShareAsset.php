<?php
namespace verbb\socialshare\web\assets\cp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

use verbb\base\web\assets\cp\CpAsset as VerbbCpAsset;

class SocialShareAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init(): void
    {
        $this->sourcePath = '@verbb/socialshare/web/assets/cp/dist';

        $this->depends = [
            VerbbCpAsset::class,
            CpAsset::class,
        ];

        $this->css = [
            'social-share.css',
        ];

        parent::init();
    }
}
