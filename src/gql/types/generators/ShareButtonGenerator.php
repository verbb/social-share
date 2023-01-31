<?php
namespace verbb\socialshare\gql\types\generators;

use verbb\socialshare\gql\interfaces\ShareButtonInterface;
use verbb\socialshare\gql\types\ShareButtonType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class ShareButtonGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $sourceFields = ShareButtonInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new ShareButtonType([
            'name' => $typeName,
            'fields' => function() use ($sourceFields) {
                return $sourceFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'ShareButtonType';
    }
}
