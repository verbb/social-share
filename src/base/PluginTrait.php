<?php
namespace verbb\socialshare\base;

use verbb\socialshare\SocialShare;
use verbb\socialshare\services\Providers;
use verbb\socialshare\services\Service;

use Craft;

use yii\log\Logger;

use verbb\auth\Auth;
use verbb\base\BaseHelper;

trait PluginTrait
{
    // Static Properties
    // =========================================================================

    public static SocialShare $plugin;


    // Public Methods
    // =========================================================================

    public static function log($message, $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('social-share', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'social-share');
    }

    public static function error($message, $attributes = []): void
    {
        if ($attributes) {
            $message = Craft::t('social-share', $message, $attributes);
        }

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'social-share');
    }


    // Public Methods
    // =========================================================================

    public function getProviders(): Providers
    {
        return $this->get('providers');
    }

    public function getService(): Service
    {
        return $this->get('service');
    }


    // Private Methods
    // =========================================================================

    private function _registerComponents(): void
    {
        $this->setComponents([
            'providers' => Providers::class,
            'service' => Service::class,
        ]);

        Auth::registerModule();
        BaseHelper::registerModule();
    }

    private function _registerLogTarget(): void
    {
        BaseHelper::setFileLogging('social-share');
    }

}