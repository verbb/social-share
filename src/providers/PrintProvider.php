<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\web\assets\frontend\ShareButtonAsset;

use Craft;

class PrintProvider extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'print';


    // Public Methods
    // =========================================================================

    public function getButtonAttributes(array $attributes): array
    {
        Craft::$app->getView()->registerAssetBundle(ShareButtonAsset::class);
        unset(
            $attributes['data-social-share-popup'],
            $attributes['data-url'],
            $attributes['target'],
            $attributes['rel'],
        );

        $attributes['href'] = '#';
        $attributes['data-social-share-print'] = true;

        return $attributes;
    }

}
