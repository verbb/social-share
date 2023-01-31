<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\OAuthProvider;

use Craft;

use Throwable;

use verbb\auth\providers\Dribbble as DribbbleProvider;

class Dribbble extends OAuthProvider
{
    // Static Methods
    // =========================================================================

    public static function hasSettings(): bool
    {
        return true;
    }

    public static function supportsFollowersCount(): bool
    {
        return true;
    }

    public static function getOAuthProviderClass(): string
    {
        return DribbbleProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'dribbble';


    // Public Methods
    // =========================================================================

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('social-share/providers/dribbble', [
            'provider' => $this,
        ]);
    }

    public function getFollowersCount(string $account): ?int
    {
        try {
            $response = $this->request('GET', 'user');

            return $response['followers_count'] ?? null;
        } catch (Throwable $e) {
            OAuthProvider::error($this, Craft::t('social-share', 'API error: “{message}” {file}:{line}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]));
        }

        return null;
    }

}