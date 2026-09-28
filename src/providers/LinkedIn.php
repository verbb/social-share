<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use craft\helpers\UrlHelper;

class LinkedIn extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'linkedIn';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://www.linkedin.com/shareArticle', array_filter(array_merge([
            'url' => $url,
            'text' => $text,
            'mini' => true,
        ], $params)));
    }

}
