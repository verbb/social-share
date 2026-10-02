<?php
namespace verbb\socialshare\services;

use verbb\socialshare\SocialShare;
use verbb\socialshare\models\Button;
use verbb\socialshare\models\ShareButton;
use verbb\socialshare\providers\Dribbble;
use verbb\socialshare\providers\Envato;
use verbb\socialshare\providers\Instagram;
use verbb\socialshare\providers\Mailchimp;

use Craft;
use craft\base\Component;
use craft\helpers\ConfigHelper;
use craft\helpers\DateTimeHelper;

use WeakMap;

use Twig\Markup;

class Service extends Component
{
    // Constants
    // =========================================================================

    public const REQUEST_SOURCE_GRAPHQL = 'graphql';
    public const MAX_GRAPHQL_ACCOUNT_LENGTH = 255;
    public const MAX_GRAPHQL_URL_LENGTH = 2048;

    private const GRAPHQL_AGGREGATE_REQUEST_CACHE_KEY = 'social-share.graphql-aggregate-request';
    private const GRAPHQL_AGGREGATE_REQUEST_MUTEX_KEY = 'social-share.graphql-aggregate-request-lock';
    private const GRAPHQL_COUNT_CACHE_TAG = 'social-share.graphql-count';
    private const GRAPHQL_PROVIDER_REQUEST_CACHE_PREFIX = 'social-share.graphql-provider-request.';
    private const GRAPHQL_PROVIDER_REQUEST_MUTEX_PREFIX = 'social-share.graphql-provider-request-lock.';


    // Properties
    // =========================================================================

    private ?WeakMap $_graphqlOperationRequestCounts = null;


    // Public Methods
    // =========================================================================

    public function getFollowers(string $handle, string $account, array $options = []): ?string
    {
        $settings = SocialShare::$plugin->getSettings();
        $isGraphqlRequest = ($options['requestSource'] ?? null) === self::REQUEST_SOURCE_GRAPHQL;
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider || !$provider::supportsFollowersCount()) {
            $this->_registerGraphqlCacheInfo(null, $settings->cacheDuration, $isGraphqlRequest);

            return null;
        }

        if (($provider instanceof Envato || $provider instanceof Mailchimp) && !$provider->isConfigured()) {
            $this->_registerGraphqlCacheInfo(null, $settings->cacheDuration, $isGraphqlRequest);

            return null;
        }

        if ($isGraphqlRequest && strlen($account) > self::MAX_GRAPHQL_ACCOUNT_LENGTH) {
            $this->_registerGraphqlCacheInfo(null, $settings->cacheDuration, true);

            return null;
        }

        // These providers always query the connected account, so caller input must not fragment the cache.
        if ($provider instanceof Dribbble || $provider instanceof Instagram) {
            $account = $provider->getHandle();
        }

        // Caching options can be set from Twig
        $cacheKey = md5('social-share:' . $handle . ':' . $account);
        $enableCache = $options['enableCache'] ?? $settings->enableCache;
        $cacheDuration = $options['cacheDuration'] ?? $settings->cacheDuration;
        $friendlyCount = $options['friendlyCount'] ?? $settings->friendlyCount;

        // Should we be caching?
        if ($enableCache) {
            $cache = Craft::$app->getCache()->get($cacheKey);

            if ($cache !== false) {
                $this->_registerGraphqlCacheInfo($cache, $cacheDuration, $isGraphqlRequest);

                if ($friendlyCount) {
                    return $this->_formatNumber($cache);
                }

                return $cache;
            }
        }

        if ($isGraphqlRequest && !$this->_consumeGraphqlRequestBudget($provider->getHandle(), $options['graphqlOperation'] ?? null)) {
            $this->_registerGraphqlCacheInfo(null, $cacheDuration, true, $settings->graphqlProviderRequestWindow);

            return null;
        }

        // Cache not enabled or value not cached, so fetch the value
        $count = $provider->getFollowersCount($account);

        // Then, maybe save to cache
        if ($enableCache) {
            $this->_cacheCount($cacheKey, $count, $cacheDuration, $isGraphqlRequest);
        } else {
            $this->_registerGraphqlCacheInfo($count, $cacheDuration, $isGraphqlRequest);
        }

        if ($friendlyCount) {
            return $this->_formatNumber($count);
        }

        return $count;
    }

    public function getShares(string $handle, string $url, array $options = []): ?string
    {
        $settings = SocialShare::$plugin->getSettings();
        $isGraphqlRequest = ($options['requestSource'] ?? null) === self::REQUEST_SOURCE_GRAPHQL;
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider || !$provider::supportsSharesCount()) {
            $this->_registerGraphqlCacheInfo(null, $settings->cacheDuration, $isGraphqlRequest);

            return null;
        }

        if ($isGraphqlRequest && strlen($url) > self::MAX_GRAPHQL_URL_LENGTH) {
            $this->_registerGraphqlCacheInfo(null, $settings->cacheDuration, true);

            return null;
        }

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
                $this->_registerGraphqlCacheInfo($cache, $cacheDuration, $isGraphqlRequest);

                if ($settings->minShareCount && $cache < $settings->minShareCount) {
                    return null;
                }

                if ($friendlyCount) {
                    return $this->_formatNumber($cache);
                }

                return $cache;
            }
        }

        if ($isGraphqlRequest && !$this->_consumeGraphqlRequestBudget($provider->getHandle(), $options['graphqlOperation'] ?? null)) {
            $this->_registerGraphqlCacheInfo(null, $cacheDuration, true, $settings->graphqlProviderRequestWindow);

            return null;
        }

        // Cache not enabled or value not cached, so fetch the value
        $count = $provider->getSharesCount($url);

        // Then, maybe save to cache
        if ($enableCache) {
            $this->_cacheCount($cacheKey, $count, $cacheDuration, $isGraphqlRequest);
        } else {
            $this->_registerGraphqlCacheInfo($count, $cacheDuration, $isGraphqlRequest);
        }

        if ($settings->minShareCount && $count < $settings->minShareCount) {
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

    private function _cacheCount(string $cacheKey, ?int $count, mixed $cacheDuration, bool $isGraphqlRequest): void
    {
        $settings = SocialShare::$plugin->getSettings();
        $duration = ConfigHelper::durationInSeconds($cacheDuration);

        if ($isGraphqlRequest && ($count === null || $count <= 0)) {
            $negativeDuration = max(1, $settings->graphqlNegativeCacheDuration);
            $duration = $duration > 0 ? min($duration, $negativeDuration) : $negativeDuration;
        }

        Craft::$app->getCache()->set($cacheKey, $count, $duration);
        $this->_registerGraphqlCacheInfo($count, $cacheDuration, $isGraphqlRequest);
    }

    private function _registerGraphqlCacheInfo(?int $count, mixed $cacheDuration, bool $isGraphqlRequest, ?int $maximumDuration = null): void
    {
        if (!$isGraphqlRequest) {
            return;
        }

        $settings = SocialShare::$plugin->getSettings();
        $duration = ConfigHelper::durationInSeconds($cacheDuration);

        if ($count === null || $count <= 0) {
            $negativeDuration = max(1, $settings->graphqlNegativeCacheDuration);
            $duration = $duration > 0 ? min($duration, $negativeDuration) : $negativeDuration;
        }

        if ($maximumDuration !== null) {
            $maximumDuration = max(1, $maximumDuration);
            $duration = $duration > 0 ? min($duration, $maximumDuration) : $maximumDuration;
        }

        // Craft discards a collected expiry when there are no dependency tags, so add a stable tag as well.
        $elements = Craft::$app->getElements();
        $elements->collectCacheTags([self::GRAPHQL_COUNT_CACHE_TAG]);

        if ($duration > 0) {
            $elements->setCacheExpiryDate(DateTimeHelper::now()->modify(sprintf('+%d seconds', $duration)));
        }
    }

    private function _consumeGraphqlRequestBudget(string $providerHandle, mixed $operation): bool
    {
        if (!is_object($operation)) {
            return false;
        }

        $settings = SocialShare::$plugin->getSettings();
        $operationLimit = max(1, $settings->graphqlOperationRequestLimit);
        $this->_graphqlOperationRequestCounts ??= new WeakMap();
        $operationCount = $this->_graphqlOperationRequestCounts[$operation] ?? 0;

        if ($operationCount >= $operationLimit) {
            return false;
        }

        if (!$this->_consumeGraphqlSharedRequestBudgets(
            $providerHandle,
            max(1, $settings->graphqlAggregateRequestLimit),
            max(1, $settings->graphqlProviderRequestLimit),
            max(1, $settings->graphqlProviderRequestWindow),
        )) {
            return false;
        }

        $this->_graphqlOperationRequestCounts[$operation] = $operationCount + 1;

        return true;
    }

    private function _consumeGraphqlSharedRequestBudgets(string $providerHandle, int $aggregateLimit, int $providerLimit, int $window): bool
    {
        $providerKeyHash = md5($providerHandle);
        $budgets = [
            'aggregate' => [
                'cacheKey' => self::GRAPHQL_AGGREGATE_REQUEST_CACHE_KEY,
                'mutexKey' => self::GRAPHQL_AGGREGATE_REQUEST_MUTEX_KEY,
                'limit' => $aggregateLimit,
            ],
            'provider' => [
                'cacheKey' => self::GRAPHQL_PROVIDER_REQUEST_CACHE_PREFIX . $providerKeyHash,
                'mutexKey' => self::GRAPHQL_PROVIDER_REQUEST_MUTEX_PREFIX . $providerKeyHash,
                'limit' => $providerLimit,
            ],
        ];
        $cache = Craft::$app->getCache();
        $mutex = Craft::$app->getMutex();
        $now = time();
        $acquiredLocks = [];

        try {
            // Every process takes the aggregate lock first to avoid cross-provider deadlocks.
            foreach ($budgets as $budget) {
                if (!($mutex?->acquire($budget['mutexKey'], 3) ?? false)) {
                    return false;
                }

                $acquiredLocks[] = $budget['mutexKey'];
            }

            $entries = [];
            $previousEntries = [];

            foreach ($budgets as $name => $budget) {
                $storedEntry = $cache->get($budget['cacheKey']);
                $isCurrentEntry = is_array($storedEntry) && isset($storedEntry['count'], $storedEntry['resetAt']) && (int)$storedEntry['resetAt'] > $now;
                $entry = $isCurrentEntry ? $storedEntry : [
                    'count' => 0,
                    'resetAt' => $now + $window,
                ];

                if ((int)$entry['count'] >= $budget['limit']) {
                    return false;
                }

                $previousEntries[$name] = $isCurrentEntry ? $storedEntry : false;
                $entry['count'] = (int)$entry['count'] + 1;
                $entries[$name] = $entry;
            }

            $writtenBudgets = [];

            foreach ($budgets as $name => $budget) {
                $resetAt = max($now + 1, (int)$entries[$name]['resetAt']);

                if (!$cache->set($budget['cacheKey'], $entries[$name], max(1, $resetAt - $now))) {
                    $this->_restoreGraphqlRequestBudgets($budgets, $previousEntries, $writtenBudgets, $now);

                    return false;
                }

                $writtenBudgets[] = $name;
            }

            foreach ($budgets as $name => $budget) {
                if ($cache->get($budget['cacheKey']) !== $entries[$name]) {
                    $this->_restoreGraphqlRequestBudgets($budgets, $previousEntries, $writtenBudgets, $now);

                    return false;
                }
            }

            return true;
        } finally {
            foreach (array_reverse($acquiredLocks) as $mutexKey) {
                $mutex?->release($mutexKey);
            }
        }
    }

    private function _restoreGraphqlRequestBudgets(array $budgets, array $previousEntries, array $writtenBudgets, int $now): void
    {
        $cache = Craft::$app->getCache();

        foreach (array_reverse($writtenBudgets) as $name) {
            $cacheKey = $budgets[$name]['cacheKey'];
            $previousEntry = $previousEntries[$name];

            if ($previousEntry === false) {
                $cache->delete($cacheKey);

                continue;
            }

            $resetAt = max($now + 1, (int)$previousEntry['resetAt']);
            $cache->set($cacheKey, $previousEntry, max(1, $resetAt - $now));
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
