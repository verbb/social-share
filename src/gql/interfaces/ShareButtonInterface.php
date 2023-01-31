<?php
namespace verbb\socialshare\gql\interfaces;

use verbb\socialshare\gql\types\generators\ShareButtonGenerator;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class ShareButtonInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return ShareButtonGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::class)) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::class, new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all share buttons.',
            'resolveType' => function ($value) {
                return GqlEntityRegistry::getEntity(ShareButtonGenerator::getName());
            },
        ]));

        ShareButtonGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'ShareButtonInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return Craft::$app->getGql()->prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The button provider’s name.',
            ],
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'The button provider’s handle.',
            ],
            'primaryColor' => [
                'name' => 'primaryColor',
                'type' => Type::string(),
                'description' => 'The button provider’s primary brand color.',
            ],
            'icon' => [
                'name' => 'icon',
                'type' => Type::string(),
                'description' => 'The button provider’s SVG icon.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The button’s URL.',
                'resolve' => function($button) {
                    return $button->getProviderUrl();
                },
            ],
        ]), self::getName());
    }
}
