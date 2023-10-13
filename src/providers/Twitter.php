<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class Twitter extends Provider
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

    public static string $handle = 'twitter';
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
        return Craft::$app->getView()->renderTemplate('social-share/providers/twitter', [
            'provider' => $this,
        ]);
    }

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://twitter.com/intent/tweet', array_filter(array_merge([
            'url' => $url,
            'text' => $text,
        ], $params)));
    }

    public function getFollowersCount(string $account): ?int
    {
        try {
            $client = Craft::createGuzzleClient();

            $accessTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth2/token', [
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($this->getClientId() . ':' . $this->getClientSecret()),
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
                ],
            ]);

            $accessTokenResponse = Json::decode((string)$accessTokenResponse->getBody());
            $accessToken = $accessTokenResponse['access_token'] ?? null;

            $response = $client->get('https://api.twitter.com/1.1/users/show.json', [
                'query' => [
                    'screen_name' => $account,
                ],
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['followers_count'] ?? null;
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

            $response = $client->get('https://api.sharedcount.com/v1.0/', [
                'query' => [
                    'url' => $url,
                    'apikey' => '1934f519a63e142e0d3c893e59cc37fe0172e98a',
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['Twitter'] ?? null;
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