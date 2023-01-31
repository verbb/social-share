<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\Json;

use Throwable;

class Mailchimp extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'mailchimp';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $accessToken = 'f37d643ee6d59070c09a8a196e204eb2-us17';
            $server = explode('-', $accessToken);
            $host = end($server);

            $response = $client->get("https://$host.api.mailchimp.com/3.0/lists/$account", [
                'headers' => [
                    'Authorization' => 'apikey ' . $accessToken,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['stats']['member_count'] ?? null;
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