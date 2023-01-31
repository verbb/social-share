<?php
namespace verbb\socialshare\gql\types;

use verbb\socialshare\gql\interfaces\ShareButtonInterface;

use craft\gql\base\ObjectType;

class ShareButtonType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            ShareButtonInterface::getType(),
        ];

        parent::__construct($config);
    }
}
