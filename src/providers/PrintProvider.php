<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use craft\helpers\UrlHelper;

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
        // Override the modal behaviour
        $attributes['onclick'] = 'window.print();';

        return $attributes;
    }

}