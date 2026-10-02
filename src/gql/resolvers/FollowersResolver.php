<?php
namespace verbb\socialshare\gql\resolvers;

use verbb\socialshare\SocialShare;
use verbb\socialshare\services\Service;

use craft\gql\base\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

use Exception;

class FollowersResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        if (!array_key_exists('handle', $arguments)) {
            throw new Exception('You must provide a `handle` argument to query `followers`.');
        }

        if (!array_key_exists('account', $arguments)) {
            throw new Exception('You must provide an `account` argument to query `followers`.');
        }

        $options = [
            'requestSource' => Service::REQUEST_SOURCE_GRAPHQL,
            'graphqlOperation' => $resolveInfo->operation,
        ];

        if (array_key_exists('friendlyCount', $arguments)) {
            $options['friendlyCount'] = $arguments['friendlyCount'];
        }

        return SocialShare::$plugin->getService()->getFollowers($arguments['handle'], $arguments['account'], $options);
    }
}
