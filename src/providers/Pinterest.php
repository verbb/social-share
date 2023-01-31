<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class Pinterest extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }

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

    public static string $handle = 'pinterest';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://pinterest.com/pin/create/button', array_filter(array_merge([
            'url' => $url,
            'description' => $text,
        ], $params)));
    }

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get("https://www.pinterest.com/{$account}");
            $html = (string)$response->getBody();

            preg_match('/pinterestapp:followers.*content="(\d*)"/m', $html, $matches);
            $value = $matches[1] ?? null;

            if ($value) {
                return (int)$value;
            }
        } catch (Throwable $e) {
            Provider::error($this, Craft::t('social-share', 'API error: “{message}” {file}:{line}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]));
        }

        return null;
    }

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get('https://widgets.pinterest.com/v1/urls/count.json', [
                'query' => [
                    'url' => $url,
                ],
            ]);

            // The response back is not quite JSON...
            $response = (string)$response->getBody();
            $response = str_replace(['receiveCount(', ')'], '', $response);
            $response = Json::decode($response);

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