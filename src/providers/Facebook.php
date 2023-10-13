<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

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
            $client = Craft::createGuzzleClient();

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

            // Convert from 13K, 24.4M, etc
            if (str_contains($value, 'K')) {
                $value = str_replace('K', '', $value) * 1000;
            } else if (str_contains($value, 'M')) {
                $value = str_replace('M', '', $value) * 1000000;
            } else if (str_contains($value, 'B')) {
                $value = str_replace('B', '', $value) * 1000000000;
            } else if (str_contains($value, 'T')) {
                $value = str_replace('T', '', $value) * 1000000000000;
            }

            if ($value) {
                return (int)$value;
            }
        } catch (Throwable $e) {
            Provider::error($this, Craft::t('social-share', 'API error: “{message}” {file}:{line}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]));
        }

        return null;
    }

    public function getSharesCount(string $url): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/oauth/access_token', [
                'query' => [
                    'client_id' => $this->getClientId(),
                    'client_secret' => $this->getClientSecret(),
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $accessTokenResponse = Json::decode((string)$accessTokenResponse->getBody());
            $accessToken = $accessTokenResponse['access_token'] ?? null;

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
            Provider::error($this, Craft::t('social-share', 'API error: “{message}” {file}:{line}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]));
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