<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderHttp;
use verbb\socialshare\helpers\ProviderLog;

use craft\helpers\Json;

use Throwable;

class GitHub extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'gitHub';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = ProviderHttp::createClient();

            $response = $client->get("https://api.github.com/users/$account");

            $response = Json::decode((string)$response->getBody());

            return $response['followers'] ?? null;
        } catch (Throwable $e) {
            ProviderLog::apiError($this, $e);
        }

        return null;
    }

}
