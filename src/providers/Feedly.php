<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

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
            Provider::error($this, Craft::t('social-share', 'API error: “{message}” {file}:{line}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]));
        }

        return null;
    }

}