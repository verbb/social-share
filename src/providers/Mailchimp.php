<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderHttp;
use verbb\socialshare\helpers\ProviderLog;

use craft\helpers\App;
use craft\helpers\Json;

use Throwable;

class Mailchimp extends Provider
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


    // Properties
    // =========================================================================

    public static string $handle = 'mailchimp';
    public ?string $apiKey = null;


    // Public Methods
    // =========================================================================

    public function getApiKey(): ?string
    {
        $apiKey = App::parseEnv($this->apiKey);

        return is_string($apiKey) ? $apiKey : null;
    }

    public function isConfigured(): bool
    {
        return (bool)$this->getApiKey();
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('social-share/providers/mailchimp', [
            'provider' => $this,
        ]);
    }

    public function getFollowersCount(string $account): ?int
    {
        $apiKey = $this->getApiKey();

        if (!$apiKey || !preg_match('/-([a-z]{2}\d+)$/i', $apiKey, $matches)) {
            return null;
        }

        try {
            $client = ProviderHttp::createClient();

            $host = strtolower($matches[1]);

            $response = $client->get("https://$host.api.mailchimp.com/3.0/lists/$account", [
                'headers' => [
                    'Authorization' => 'apikey ' . $apiKey,
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['stats']['member_count'] ?? null;
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
        $rules[] = [['apiKey'], 'required'];

        return $rules;
    }

}
