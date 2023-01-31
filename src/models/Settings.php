<?php
namespace verbb\socialshare\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Social Share';
    public bool $hasCpSection = false;
    public bool $enableCache = true;
    public mixed $cacheDuration = 86400; // 1 day
    public bool $friendlyCount = true;
    public ?int $minShareCount = null;
    public bool $useModalForShare = true;

    public array $buttonAttributes = [];
    public array $iconWrapperAttributes = [];
    public array $labelAttributes = [];
    public array $labelWrapperAttributes = [];
    public array $contentAttributes = [];

    public array $providers = [];

}