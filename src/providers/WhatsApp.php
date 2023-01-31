<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use craft\helpers\UrlHelper;

class WhatsApp extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'whatsApp';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        $string = implode('+', array_filter([$text, $url]));

        return UrlHelper::urlWithParams('https://api.whatsapp.com/send', array_filter(array_merge([
            'text' => $string,
        ], $params)));
    }

}