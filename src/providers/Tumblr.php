<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class Tumblr extends Provider
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

    public static string $handle = 'tumblr';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://www.tumblr.com/widgets/share/tool', array_filter(array_merge([
            'canonicalUrl' => $url,
            'title' => $text,
        ], $params)));
    }

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get('https://api.tumblr.com/v2/share/stats', [
                'query' => [
                    'url' => $url,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['response']['note_count'] ?? null;
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