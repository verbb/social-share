<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;

use Throwable;

class YouTube extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }

    // Properties
    // =========================================================================

    public static string $handle = 'youTube';


    // Public Methods
    // =========================================================================

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $url = "https://www.youtube.com/{$account}";

            if (!str_starts_with($account, '@')) {
                $url = "https://www.youtube.com/channel/{$account}";
            }

            $response = $client->get($url);
            $html = (string)$response->getBody();

            preg_match('/subscriberCountText.*"simpleText":"(.*) subscribers"/m', $html, $matches);
            $value = $matches[1] ?? null;

            // Convert from 13K, 24.4M, etc
            if (str_contains($value, 'K')) {
                $value = str_replace('K', '', $value) * 1000;
            } else if (str_contains($value, 'M')) {
                $value = str_replace('M', '', $value) * 1000000;
            } else if (str_contains($value, 'B')) {
                $value = str_replace('B', '', $value) * 1000000000;
            } else if (str_contains($value, 'T')) {
                $value = str_replace('T', '', $value) * 1000000000000;
            }

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