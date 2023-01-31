<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\OAuthProvider;

use Craft;

use Throwable;

use verbb\auth\providers\Facebook as InstagramProvider;

class Instagram extends OAuthProvider
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
        return InstagramProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $handle = 'instagram';


    // Public Methods
    // =========================================================================

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();
        $config['graphApiVersion'] = 'v15.0';

        return $config;
    }

    public function getAuthorizationUrlOptions(): array
    {
        return [
            'scope' => [
                'instagram_basic',
                'pages_read_engagement',
                'pages_show_list',
            ],
        ];
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('social-share/providers/instagram', [
            'provider' => $this,
        ]);
    }

    public function getFollowersCount(string $account): ?int
    {
        try {
            $count = null;

            $response = $this->request('GET', 'me/accounts', [
                'query' => ['fields' => 'instagram_business_account'],
            ]);

            $pages = $response['data'] ?? [];

            foreach ($pages as $page) {
                $instagramAccountId = $page['instagram_business_account']['id'] ?? null;

                if ($instagramAccountId) {
                    $response = $this->request('GET', $instagramAccountId, [
                        'query' => ['fields' => 'followers_count'],
                    ]);

                    $count = $response['followers_count'] ?? null;

                    break;
                }
            }

            return $count;
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