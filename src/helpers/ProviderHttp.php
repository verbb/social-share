<?php
namespace verbb\socialshare\helpers;

use Craft;

use GuzzleHttp\Client;

class ProviderHttp
{
    // Static Methods
    // =========================================================================

    public static function createClient(array $config = []): Client
    {
        return Craft::createGuzzleClient(self::_withTimeoutDefaults($config));
    }

    public static function withTimeouts(Client $client): Client
    {
        return Craft::createGuzzleClient(self::_withTimeoutDefaults($client->getConfig()));
    }

    private static function _withTimeoutDefaults(array $config): array
    {
        $siteConfig = Craft::$app->getConfig()->getConfigFromFile('guzzle');

        // Site and provider-specific policies remain authoritative over these safe defaults.
        foreach (['connect_timeout' => 3, 'timeout' => 5] as $option => $default) {
            if (!array_key_exists($option, $siteConfig) && !array_key_exists($option, $config)) {
                $config[$option] = $default;
            }
        }

        return $config;
    }
}
