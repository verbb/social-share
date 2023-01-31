<?php
namespace verbb\socialshare\services;

use verbb\socialshare\SocialShare;
use verbb\socialshare\models\Button;
use verbb\socialshare\models\ShareButton;

use Craft;
use craft\base\Component;

use Twig\Markup;

class Service extends Component
{
    // Public Methods
    // =========================================================================

    public function getFollowers(string $handle, string $account, array $options = []): ?string
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider) {
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
            if (($cache = Craft::$app->getCache()->get($cacheKey))) {
                if ($friendlyCount) {
                    return $this->_formatNumber($cache);
                }

                return $cache;
            }
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

        if (!$provider) {
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
            if (($cache = Craft::$app->getCache()->get($cacheKey))) {
                if ($settings->minShareCount && $cache < $settings->minShareCount) {
                    return null;
                }

                if ($friendlyCount) {
                    return $this->_formatNumber($cache);
                }

                return $cache;
            }
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