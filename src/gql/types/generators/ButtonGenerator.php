<?php
namespace verbb\socialshare\gql\types\generators;

use verbb\socialshare\gql\interfaces\ButtonInterface;
use verbb\socialshare\gql\types\ButtonType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class ButtonGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $sourceFields = ButtonInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new ButtonType([
            'name' => $typeName,
            'fields' => function() use ($sourceFields) {
                return $sourceFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'ButtonType';
    }
}
