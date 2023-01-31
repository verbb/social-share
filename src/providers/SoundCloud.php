<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;

use Throwable;

class SoundCloud extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'soundCloud';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get("https://soundcloud.com/$account");
            $html = (string)$response->getBody();

            preg_match('/<meta property="soundcloud:follower_count" content="(.*)">/s', $html, $matches);
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

}