<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use craft\helpers\UrlHelper;

class Parler extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'parler';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://parler.com/new-post', array_filter(array_merge([
            'url' => $url,
            'message' => $text,
        ], $params)));
    }

}