<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderHttp;
use verbb\socialshare\helpers\ProviderLog;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use RuntimeException;
use Throwable;

class Facebook extends Provider
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

    public static function supportsSharesCount(): bool
    {
        return true;
    }

    public static function supportsShareButton(): bool
    {
        return true;
    }


    // Constants
    // =========================================================================

    private const APP_ACCESS_TOKEN_CACHE_DURATION = 3600;


    // Properties
    // =========================================================================

    public static string $handle = 'facebook';
    public ?string $clientId = null;
    public ?string $clientSecret = null;


    // Public Methods
    // =========================================================================

    public function getClientId(): string
    {
        return App::parseEnv($this->clientId);
    }

    public function getClientSecret(): string
    {
        return App::parseEnv($this->clientSecret);
    }

    public function isConfigured(): bool
    {
        return $this->clientId && $this->clientSecret;
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('social-share/providers/facebook', [
            'provider' => $this,
        ]);
    }

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://www.facebook.com/sharer/sharer.php', array_filter(array_merge([
            'u' => $url,
        ], $params)));
    }

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = ProviderHttp::createClient();

            $response = $client->request('GET', 'https://www.facebook.com/plugins/likebox.php', [
                'query' => [
                    'href' => "https://facebook.com/{$account}",
                    'show_faces' => true,
                    'header' => false,
                    'stream' => false,
                    'show_border' => false,
                    'locale' => 'en_US',
                ],
            ]);

            $html = (string)$response->getBody();

            preg_match('/<\/div>(\d.*) likes/m', $html, $matches);
            $value = $matches[1] ?? null;

            if ($value === null) {
                return null;
            }

            // Convert from 13K, 24.4M, etc
            if (str_contains($value, 'K')) {
                $value = str_replace('K', '', $value) * 1000;
            } elseif (str_contains($value, 'M')) {
                $value = str_replace('M', '', $value) * 1000000;
            } elseif (str_contains($value, 'B')) {
                $value = str_replace('B', '', $value) * 1000000000;
            } elseif (str_contains($value, 'T')) {
                $value = str_replace('T', '', $value) * 1000000000000;
            }

            if ($value) {
                return (int)$value;
            }
        } catch (Throwable $e) {
            ProviderLog::apiError($this, $e);
        }

        return null;
    }

    public function getSharesCount(string $url): ?int
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $client = ProviderHttp::createClient();
            $clientId = $this->getClientId();
            $clientSecret = $this->getClientSecret();
            $accessTokenCacheKey = [
                'social-share.facebook-app-token',
                hash('sha256', $clientId . "\0" . $clientSecret),
            ];
            $accessToken = Craft::$app->getCache()->getOrSet($accessTokenCacheKey, function() use ($client, $clientId, $clientSecret): string {
                $response = $client->request('GET', 'https://graph.facebook.com/oauth/access_token', [
                    'query' => [
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                        'grant_type' => 'client_credentials',
                    ],
                ]);
                $response = Json::decode((string)$response->getBody());
                $accessToken = $response['access_token'] ?? null;

                if (!is_string($accessToken) || $accessToken === '') {
                    throw new RuntimeException('Facebook returned no app access token.');
                }

                return $accessToken;
            }, self::APP_ACCESS_TOKEN_CACHE_DURATION);

            $response = $client->get('https://graph.facebook.com', [
                'query' => [
                    'id' => $url,
                    'fields' => 'engagement',
                    'access_token' => $accessToken,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            $count = 0;
            $count += $response['engagement']['share_count'] ?? 0;
            $count += $response['engagement']['reaction_count'] ?? 0;
            $count += $response['engagement']['comment_count'] ?? 0;

            return $count ?: null;
        } catch (Throwable $e) {
            ProviderLog::apiError($this, $e);
        }

        return null;
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['clientId', 'clientSecret'], 'required'];

        return $rules;
    }

}
