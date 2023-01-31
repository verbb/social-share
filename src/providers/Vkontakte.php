<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\UrlHelper;

use Throwable;

class Vkontakte extends Provider
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return true;
    }

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'vkontakte';


    // Public Methods
    // =========================================================================

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://vk.com/share.php', array_filter(array_merge([
            'url' => $url,
        ], $params)));
    }

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $response = $client->get("https://m.vk.com/$account?act=fans");
            $html = (string)$response->getBody();

            preg_match('/slim_header">(.*) followers/s', $html, $matches);
            $value = $matches[1] ?? null;
            $value = str_replace(',', '', $value);

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