<?php
namespace verbb\socialshare\gql\interfaces;

use verbb\socialshare\gql\arguments\ButtonArguments;
use verbb\socialshare\gql\arguments\FollowersArguments;
use verbb\socialshare\gql\arguments\ShareButtonArguments;
use verbb\socialshare\gql\arguments\SharesArguments;
use verbb\socialshare\gql\resolvers\ButtonResolver;
use verbb\socialshare\gql\resolvers\FollowersResolver;
use verbb\socialshare\gql\resolvers\ShareButtonResolver;
use verbb\socialshare\gql\resolvers\SharesResolver;
use verbb\socialshare\gql\types\generators\SocialShareGenerator;

use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class SocialShareInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return SocialShareGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::class)) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::class, new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by Social Share.',
            'resolveType' => function ($value) {
                return GqlEntityRegistry::getEntity(SocialShareGenerator::getName());
            },
        ]));

        SocialShareGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'SocialShareInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return [
            'followers' => [
                'name' => 'followers',
                'args' => FollowersArguments::getArguments(),
                'type' => Type::string(),
                'resolve' => FollowersResolver::class . '::resolve',
                'description' => 'The number of followers for a given provider.',
            ],
            'shares' => [
                'name' => 'shares',
                'args' => SharesArguments::getArguments(),
                'type' => Type::string(),
                'resolve' => SharesResolver::class . '::resolve',
                'description' => 'The number of shares for a given URL for a provider.',
            ],
            'shareButtons' => [
                'name' => 'shareButtons',
                'args' => ShareButtonArguments::getArguments(),
                'type' => Type::listOf(ShareButtonInterface::getType()),
                'resolve' => ShareButtonResolver::class . '::resolve',
                'description' => 'All Social Share share buttons.',
            ],
            'shareButton' => [
                'name' => 'shareButton',
                'args' => ShareButtonArguments::getArguments(),
                'type' => ShareButtonInterface::getType(),
                'resolve' => ShareButtonResolver::class . '::resolveOne',
                'description' => 'A single Social Share share button.',
            ],
            'buttons' => [
                'name' => 'buttons',
                'args' => ButtonArguments::getArguments(),
                'type' => Type::listOf(ButtonInterface::getType()),
                'resolve' => ButtonResolver::class . '::resolve',
                'description' => 'All Social Share buttons.',
            ],
            'button' => [
                'name' => 'button',
                'args' => ButtonArguments::getArguments(),
                'type' => ButtonInterface::getType(),
                'resolve' => ButtonResolver::class . '::resolveOne',
                'description' => 'A single Social Share button.',
            ],
        ];
    }
}
