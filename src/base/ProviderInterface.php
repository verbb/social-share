<?php
namespace verbb\socialshare\base;

use verbb\socialshare\models\ShareButton;

use craft\base\SavableComponentInterface;

use Twig\Markup;

interface ProviderInterface extends SavableComponentInterface
{
    // Static Methods
    // =========================================================================

    public static function supportsFollowersCount(): bool;
    public static function supportsSharesCount(): bool;
    public static function supportsShareButton(): bool;
    public static function hasSettings(): bool;


    // Public Methods
    // =========================================================================

    public function getName(): string;
    public function getHandle(): string;
    public function isConfigured(): bool;
    public function getSettingsHtml(): ?string;
    public function getFollowersCount(string $account): ?int;
    public function getSharesCount(string $url): ?int;
    public function getPrimaryColor(): ?string;
    public function getIcon(): ?string;
    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string;
    public function getShareButton(array $options = []): ?ShareButton;
    public function renderShareButton(array $options = []): ?Markup;

}
