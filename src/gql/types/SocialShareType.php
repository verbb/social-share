<?php
namespace verbb\socialshare\gql\types;

use verbb\socialshare\gql\interfaces\SocialShareInterface;

use craft\gql\base\ObjectType;

class SocialShareType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SocialShareInterface::getType(),
        ];

        parent::__construct($config);
    }
}
