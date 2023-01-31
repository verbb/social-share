<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class Yummly extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsSharesCount(): bool
    {
        return true;
    }

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'yummly';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://www.yummly.com/urb/verify', array_filter(array_merge([
            'url' => $url,
            'title' => $text,
            'yumtype' => 'button',
            'urbtype' => 'bookmarklet',
            'type' => 'agg',
        ], $params)));
    }

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get('https://www.yummly.com/services/yum-count', [
                'query' => [
                    'url' => $url,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['count'] ?? null;
        } catch (Throwable $e) {
            Provider::error($this, Craft::t('social-share', 'API error: “{message}” {file}:{line}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]));
        }

        return null;
    }

}