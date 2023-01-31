<?php
namespace verbb\socialshare\base;

use verbb\socialshare\SocialShare;
use verbb\socialshare\models\Button;
use verbb\socialshare\models\ShareButton;

use craft\base\SavableComponent;

use Exception;

use Twig\Markup;

use verbb\auth\helpers\Provider as ProviderHelper;

abstract class Provider extends SavableComponent implements ProviderInterface
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool
    {
        return false;
    }

    public static function supportsSharesCount(): bool
    {
        return false;
    }

    public static function supportsShareButton(): bool
    {
        return false;
    }

    public static function hasSettings(): bool
    {
        return false;
    }

    public static function log(Provider $provider, string $message, bool $throwError = false): void
    {
        SocialShare::log($provider->name . ': ' . $message);

        if ($throwError) {
            throw new Exception($message);
        }
    }

    public static function error(Provider $provider, string $message, bool $throwError = false): void
    {
        SocialShare::error($provider->name . ': ' . $message);

        if ($throwError) {
            throw new Exception($message);
        }
    }


    // Public Methods
    // =========================================================================

    public function getName(): string
    {
        return static::displayName();
    }

    public function getHandle(): string
    {
        return static::$handle;
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function getSettingsHtml(): ?string
    {
        return null;
    }

    public function getFollowersCount(string $account): ?int
    {
        return null;
    }

    public function getSharesCount(string $url): ?int
    {
        return null;
    }

    public function getPrimaryColor(): ?string
    {
        return ProviderHelper::getPrimaryColor(static::$handle);
    }

    public function getIcon(): ?string
    {
        return ProviderHelper::getIcon(static::$handle);
    }

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return null;
    }

    public function getShareButton(array $options = []): ?ShareButton
    {
        return new ShareButton([
            'provider' => $this,
            'renderOptions' => $options,
        ]);
    }

    public function renderShareButton(array $options = []): ?Markup
    {
        return $this->getShareButton($options)->render();
    }

    public function getButton(array $options = []): ?Button
    {
        return new Button([
            'provider' => $this,
            'renderOptions' => $options,
        ]);
    }

    public function renderButton(array $options = []): ?Markup
    {
        return $this->getButton($options)->render();
    }
}
