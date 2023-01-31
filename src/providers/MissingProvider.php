<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\base\MissingComponentInterface;
use craft\base\MissingComponentTrait;

class MissingProvider extends Provider implements MissingComponentInterface
{
    // Traits
    // =========================================================================

    use MissingComponentTrait;


    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-share', 'Missing Provider');
    }


    // Properties
    // =========================================================================

    public static string $handle = 'missingProvider';
}
