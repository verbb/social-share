<?php
namespace verbb\socialshare\gql\resolvers;

use verbb\socialshare\SocialShare;
use verbb\socialshare\services\Service;

use craft\gql\base\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

use Exception;

class SharesResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        if (!array_key_exists('handle', $arguments)) {
            throw new Exception('You must provide a `handle` argument to query `shares`.');
        }

        if (!array_key_exists('url', $arguments)) {
            throw new Exception('You must provide a `url` argument to query `shares`.');
        }

        $options = [
            'requestSource' => Service::REQUEST_SOURCE_GRAPHQL,
            'graphqlOperation' => $resolveInfo->operation,
        ];

        if (array_key_exists('friendlyCount', $arguments)) {
            $options['friendlyCount'] = $arguments['friendlyCount'];
        }

        return SocialShare::$plugin->getService()->getShares($arguments['handle'], $arguments['url'], $options);
    }
}
