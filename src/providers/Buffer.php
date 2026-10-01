<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderHttp;
use verbb\socialshare\helpers\ProviderLog;

use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class Buffer extends Provider
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

    public static string $handle = 'buffer';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://buffer.com/add', array_filter(array_merge([
            'url' => $url,
            'text' => $text,
        ], $params)));
    }

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = ProviderHttp::createClient();

            $response = $client->get('https://api.bufferapp.com/1/links/shares.json', [
                'query' => [
                    'url' => $url,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['shares'] ?? null;
        } catch (Throwable $e) {
            ProviderLog::apiError($this, $e);
        }

        return null;
    }

}
