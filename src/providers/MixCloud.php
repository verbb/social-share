<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderLog;

use Craft;
use craft\helpers\Json;

use Throwable;

class MixCloud extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'mixCloud';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get("https://api.mixcloud.com/$account");
            $response = Json::decode((string)$response->getBody());

            return $response['follower_count'] ?? null;
        } catch (Throwable $e) {
            ProviderLog::apiError($this, $e);
        }

        return null;
    }

}
