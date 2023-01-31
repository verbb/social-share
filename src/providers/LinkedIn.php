<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class LinkedIn extends Provider
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

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get('https://api.sharedcount.com/v1.0/', [
                'query' => [
                    'url' => $url,
                    'apikey' => '1934f519a63e142e0d3c893e59cc37fe0172e98a',
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['LinkedIn'] ?? null;
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