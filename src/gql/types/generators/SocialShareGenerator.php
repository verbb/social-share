<?php
namespace verbb\socialshare\gql\types\generators;

use verbb\socialshare\gql\arguments\SocialShareArguments;
use verbb\socialshare\gql\interfaces\SocialShareInterface;
use verbb\socialshare\gql\types\SocialShareType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class SocialShareGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $socialShareFields = SocialShareInterface::getFieldDefinitions();
        $socialShareArgs = SocialShareArguments::getArguments();
        
        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new SocialShareType([
            'name' => $typeName,
            'args' => function() use ($socialShareArgs) {
                return $socialShareArgs;
            },
            'fields' => function() use ($socialShareFields) {
                return $socialShareFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'SocialShareType';
    }
}
