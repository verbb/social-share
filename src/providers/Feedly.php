<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderLog;

use Craft;
use craft\helpers\Json;

use Throwable;

class Feedly extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'feedly';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();
            $response = $client->get("https://cloud.feedly.com/v3/feeds/$account");

            $response = Json::decode((string)$response->getBody());

            return $response['subscribers'] ?? null;
        } catch (Throwable $e) {
            ProviderLog::apiError($this, $e);
        }

        return null;
    }

}
