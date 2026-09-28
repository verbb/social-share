<?php
namespace verbb\socialshare\helpers;

use verbb\socialshare\SocialShare;
use verbb\socialshare\base\Provider;

use Throwable;

class ProviderLog
{
    // Static Methods
    // =========================================================================

    public static function apiError(Provider $provider, Throwable $exception): void
    {
        SocialShare::error(sprintf(
            'API request failed for provider “%s” (%s).',
            $provider->handle,
            $exception::class,
        ));
    }
}
