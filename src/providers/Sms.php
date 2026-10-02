<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use craft\helpers\UrlHelper;

class Sms extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'sms';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        $string = implode(' ', array_filter([$text, $url]));

        $shareUrl = UrlHelper::urlWithParams('sms:', array_filter(array_merge([
            'body' => $string,
        ], $params)));

        return str_replace('+', '%20', $shareUrl);
    }

}
