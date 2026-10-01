<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;
use verbb\socialshare\helpers\ProviderHttp;
use verbb\socialshare\helpers\ProviderLog;

use craft\helpers\App;
use craft\helpers\Json;

use Throwable;

class Envato extends Provider
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

    public static string $handle = 'envato';
    public ?string $personalToken = null;


    // Public Methods
    // =========================================================================

    public function getPersonalToken(): ?string
    {
        $personalToken = App::parseEnv($this->personalToken);

        return is_string($personalToken) ? $personalToken : null;
    }

    public function isConfigured(): bool
    {
        return (bool)$this->getPersonalToken();
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('social-share/providers/envato', [
            'provider' => $this,
        ]);
    }

    public function getFollowersCount(string $account): ?int
    {
        $personalToken = $this->getPersonalToken();

        if (!$personalToken) {
            return null;
        }

        try {
            $client = ProviderHttp::createClient();

            $response = $client->get("https://api.envato.com/v1/market/user:$account.json", [
                'headers' => [
                    'Authorization' => "Bearer $personalToken",
                ],
            ]);

            $response = Json::decode((string)$response->getBody());

            return $response['user']['followers'] ?? null;
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
        $rules[] = [['personalToken'], 'required'];

        return $rules;
    }

}
