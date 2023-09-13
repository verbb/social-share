<?php
namespace verbb\socialshare\providers;

use verbb\socialshare\base\Provider;

use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\helpers\UrlHelper;

use Throwable;

class X extends Twitter
{
    // Properties
    // =========================================================================

    public static string $handle = 'x';

}