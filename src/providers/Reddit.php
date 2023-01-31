<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class Reddit extends Provider
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

    public static string $handle = 'reddit';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://www.reddit.com/submit', array_filter(array_merge([
            'url' => $url,
            'title' => $text,
        ], $params)));
    }

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get('https://www.reddit.com/api/info.json', [
                'query' => [
                    'url' => $url,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            $count = 0;
            $children = $response['data']['children'] ?? [];

            foreach ($children as $child) {
                $count += $child['data']['score'] ?? 0;
            }

            return $count ?: null;
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