<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use craft\helpers\UrlHelper;

class Mailru extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'mailru';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://connect.mail.ru/share', array_filter(array_merge([
            'share_url' => $url,
        ], $params)));
    }
}