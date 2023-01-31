<?php
namespace verbb\socialshare\variables;

use verbb\socialshare\SocialShare;
use verbb\socialshare\base\ProviderInterface;
use verbb\socialshare\models\Button;
use verbb\socialshare\models\ShareButton;

use Twig\Markup;

class SocialShareVariable
{
    // Public Methods
    // =========================================================================

    public function getPlugin(): SocialShare
    {
        return SocialShare::$plugin;
    }

    public function getPluginName(): string
    {
        return SocialShare::$plugin->getPluginName();
    }

    public function getProviders(): array
    {
        return SocialShare::$plugin->getProviders()->getAllProviders();
    }

    public function getProvider(string $handle): ?ProviderInterface
    {
        return SocialShare::$plugin->getProviders()->getProviderByHandle($handle);
    }

    public function getFollowersCountProviders(): array
    {
        return SocialShare::$plugin->getProviders()->getAllFollowersCountProviders();
    }

    public function getSharesCountProviders(): array
    {
        return SocialShare::$plugin->getProviders()->getAllSharesCountProviders();
    }

    public function getShareButtonProviders(): array
    {
        return SocialShare::$plugin->getProviders()->getAllShareButtonProviders();
    }

    public function getFollowers(string $handle, string $account, array $options = []): ?string
    {
        $handle = trim($handle);
        $account = trim($account);

        return SocialShare::$plugin->getService()->getFollowers($handle, $account, $options);
    }

    public function getShares(string $handle, string $url, array $options = []): ?string
    {
        $handle = trim($handle);
        $url = trim($url);

        return SocialShare::$plugin->getService()->getShares($handle, $url, $options);
    }

    public function getShareButton(string $handle, array $options = []): ?ShareButton
    {
        $handle = trim($handle);

        return SocialShare::$plugin->getService()->getShareButton($handle, $options);
    }

    public function renderShareButton(string $handle, array $options = []): ?Markup
    {
        $handle = trim($handle);

        return SocialShare::$plugin->getService()->renderShareButton($handle, $options);
    }

    public function getButton(string $handle, array $options = []): ?Button
    {
        $handle = trim($handle);

        return SocialShare::$plugin->getService()->getButton($handle, $options);
    }

    public function renderButton(string $handle, array $options = []): ?Markup
    {
        $handle = trim($handle);

        return SocialShare::$plugin->getService()->renderButton($handle, $options);
    }
}