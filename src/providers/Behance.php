<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;

use Throwable;

class Behance extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'behance';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $accessToken = 'INekEPLWGFlXlfmWjjOZD79vWNaD1Nxj';

            $response = $client->get("https://www.behance.net/v2/users/$account", [
                'query' => [
                    'api_key' => $accessToken,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['user']['stats']['followers'] ?? null;
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