<?php
namespace verbb\socialshare\gql\types;

use verbb\socialshare\gql\interfaces\ButtonInterface;

use craft\gql\base\ObjectType;

class ButtonType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            ButtonInterface::getType(),
        ];

        parent::__construct($config);
    }
}
