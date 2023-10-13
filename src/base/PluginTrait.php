<?php
namespace verbb\socialshare\base;

use verbb\socialshare\SocialShare;
use verbb\socialshare\services\Providers;
use verbb\socialshare\services\Service;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

use verbb\auth\Auth;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static ?SocialShare $plugin = null;


    // Traits
    // =========================================================================

    use LogTrait;
    

    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Auth::registerModule();
        Plugin::bootstrapPlugin('social-share');

        return [
            'components' => [
                'providers' => Providers::class,
                'service' => Service::class,
            ],
        ];
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

}