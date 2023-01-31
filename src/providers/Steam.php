<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;

use SimpleXmlElement;
use Throwable;

class Steam extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'steam';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get("https://steamcommunity.com/groups/$account/memberslistxml?xml=1");

            $response = new SimpleXmlElement((string)$response->getBody());
            
            return (int)$response->groupDetails->memberCount;
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