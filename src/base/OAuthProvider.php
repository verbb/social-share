<?php
namespace verbb\socialshare\base;

use craft\helpers\UrlHelper;

use verbb\auth\Auth;
use verbb\auth\base\OAuthProviderInterface;
use verbb\auth\base\OAuthProviderTrait;
use verbb\auth\models\Token;

abstract class OAuthProvider extends Provider implements OAuthProviderInterface
{
    // Traits
    // =========================================================================

    use OAuthProviderTrait;
    

    // Public Methods
    // =========================================================================

    public function settingsAttributes(): array
    {
        // These won't be picked up in a Trait
        $attributes = parent::settingsAttributes();
        $attributes[] = 'clientId';
        $attributes[] = 'clientSecret';

        return $attributes;
    }

    public function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['clientId', 'clientSecret'], 'required'];

        return $rules;
    }

    public function isConfigured(): bool
    {
        return $this->clientId && $this->clientSecret;
    }

    public function isConnected(): bool
    {
        return (bool)$this->getToken();
    }

    public function getRedirectUri(): ?string
    {
        $siteId = Craft::$app->getSites()->getPrimarySite()->id;

        // We should always use the primary site for the redirect
        return UrlHelper::siteUrl('social-share/auth/callback', null, null, $siteId);
    }

    public function getToken(): ?Token
    {
        return Auth::$plugin->getTokens()->getTokenByOwnerReference('social-share', $this->handle);
    }
}