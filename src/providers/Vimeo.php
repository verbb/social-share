<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;

use Throwable;

class Vimeo extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'vimeo';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $accessToken = '6m4GyfcFCklFySPiz9DDqup1gbL9oqkj';

            $response = $client->get("https://vimeo.com/api/v2/channel/$account/info.json", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['total_subscribers'] ?? null;
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