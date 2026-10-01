<?php
namespace verbb\socialshare\services;

use verbb\socialshare\SocialShare;
use verbb\socialshare\models\Button;
use verbb\socialshare\models\ShareButton;
use verbb\socialshare\providers\Envato;
use verbb\socialshare\providers\Mailchimp;

use Craft;
use craft\base\Component;

use Twig\Markup;

class Service extends Component
{
    // Constants
    // =========================================================================

    public const REQUEST_SOURCE_GRAPHQL = 'graphql';

    private const GRAPHQL_PROVIDER_REQUEST_CACHE_PREFIX = 'social-share.graphql-provider-request.';
    private const GRAPHQL_PROVIDER_REQUEST_MUTEX_PREFIX = 'social-share.graphql-provider-request-lock.';

    // Public Methods
    // =========================================================================

    public function getFollowers(string $handle, string $account, array $options = []): ?string
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider || !$provider::supportsFollowersCount()) {
            return null;
        }

        if (($provider instanceof Envato || $provider instanceof Mailchimp) && !$provider->isConfigured()) {
            return null;
        }

        $settings = SocialShare::$plugin->getSettings();

        // Caching options can be set from Twig
        $cacheKey = md5('social-share:' . $handle . ':' . $account);
        $enableCache = $options['enableCache'] ?? $settings->enableCache;
        $cacheDuration = $options['cacheDuration'] ?? $settings->cacheDuration;
        $friendlyCount = $options['friendlyCount'] ?? $settings->friendlyCount;

        // Should we be caching?
        if ($enableCache) {
            $cache = Craft::$app->getCache()->get($cacheKey);

            if ($cache !== false) {
                if ($friendlyCount) {
                    return $this->_formatNumber($cache);
                }

                return $cache;
            }
        }

        if (($options['requestSource'] ?? null) === self::REQUEST_SOURCE_GRAPHQL && !$this->_consumeGraphqlProviderRequestBudget($provider->getHandle())) {
            return null;
        }

        // Cache not enabled or value not cached, so fetch the value
        $count = $provider->getFollowersCount($account);

        // Then, maybe save to cache
        if ($enableCache) {
            Craft::$app->getCache()->set($cacheKey, $count, $cacheDuration);
        }

        if ($friendlyCount) {
            return $this->_formatNumber($count);
        }

        return $count;
    }

    public function getShares(string $handle, string $url, array $options = []): ?string
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider || !$provider::supportsSharesCount()) {
            return null;
        }

        $settings = SocialShare::$plugin->getSettings();

        // Caching options can be set from Twig
        $cache = null;
        $cacheKey = md5('social-share:' . $handle . ':' . $url);
        $enableCache = $options['enableCache'] ?? $settings->enableCache;
        $cacheDuration = $options['cacheDuration'] ?? $settings->cacheDuration;
        $friendlyCount = $options['friendlyCount'] ?? $settings->friendlyCount;

        // Should we be caching?
        if ($enableCache) {
            $cache = Craft::$app->getCache()->get($cacheKey);

            if ($cache !== false) {
                if ($settings->minShareCount && $cache < $settings->minShareCount) {
                    return null;
                }

                if ($friendlyCount) {
                    return $this->_formatNumber($cache);
                }

                return $cache;
            }
        }

        if (($options['requestSource'] ?? null) === self::REQUEST_SOURCE_GRAPHQL && !$this->_consumeGraphqlProviderRequestBudget($provider->getHandle())) {
            return null;
        }

        // Cache not enabled or value not cached, so fetch the value
        $count = $provider->getSharesCount($url);

        // Then, maybe save to cache
        if ($enableCache) {
            Craft::$app->getCache()->set($cacheKey, $count, $cacheDuration);
        }

        if ($settings->minShareCount && $cache < $settings->minShareCount) {
            return null;
        }

        if ($friendlyCount) {
            return $this->_formatNumber($count);
        }

        return $count;
    }

    public function getShareButton(string $handle, array $options = []): ?ShareButton
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        return $provider?->getShareButton($options);
    }

    public function renderShareButton(string $handle, array $options = []): ?Markup
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        return $provider?->renderShareButton($options);
    }

    public function getButton(string $handle, array $options = []): ?Button
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        return $provider?->getButton($options);
    }

    public function renderButton(string $handle, array $options = []): ?Markup
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        return $provider?->renderButton($options);
    }


    // Private Methods
    // =========================================================================

    private function _consumeGraphqlProviderRequestBudget(string $providerHandle): bool
    {
        $settings = SocialShare::$plugin->getSettings();
        $limit = max(1, $settings->graphqlProviderRequestLimit);
        $window = max(1, $settings->graphqlProviderRequestWindow);
        $keyHash = md5($providerHandle);
        $cacheKey = self::GRAPHQL_PROVIDER_REQUEST_CACHE_PREFIX . $keyHash;
        $mutexKey = self::GRAPHQL_PROVIDER_REQUEST_MUTEX_PREFIX . $keyHash;
        $cache = Craft::$app->getCache();
        $mutex = Craft::$app->getMutex();
        $now = time();
        $lockAcquired = $mutex?->acquire($mutexKey, 3) ?? false;

        if (!$lockAcquired) {
            return false;
        }

        try {
            $entry = $cache->get($cacheKey);

            if (!is_array($entry) || !isset($entry['count'], $entry['resetAt']) || (int)$entry['resetAt'] <= $now) {
                $entry = [
                    'count' => 0,
                    'resetAt' => $now + $window,
                ];
            }

            $count = (int)$entry['count'];
            $resetAt = max($now + 1, (int)$entry['resetAt']);

            if ($count >= $limit) {
                return false;
            }

            // Record the attempt before the outbound request so provider failures still consume the budget.
            $entry['count'] = $count + 1;

            if (!$cache->set($cacheKey, $entry, max(1, $resetAt - $now))) {
                return false;
            }

            return $cache->get($cacheKey) === $entry;
        } finally {
            $mutex?->release($mutexKey);
        }
    }

    private function _formatNumber(?int $number): ?string
    {
        if ($number >= 1000 && $number < 1000000) {
            $number /= 1000;
            $number = (!is_int($number)) ? round($number, 1) : round($number);
            return $number . 'K';
        }

        if ($number >= 1000000 && $number < 1000000000) {
            $number /= 1000000;
            $number = (!is_int($number)) ? round($number, 1) : round($number);
            return $number . 'M';
        }

        if ($number >= 1000000000 && $number < 1000000000000) {
            $number /= 1000000000;
            $number = (!is_int($number)) ? round($number, 1) : round($number);
            return $number . 'B';
        }

        if ($number >= 1000000000000) {
            $number /= 1000000000000;
            $number = (!is_int($number)) ? round($number, 1) : round($number);
            return $number . 'T';
        }

        return $number;
    }

}
