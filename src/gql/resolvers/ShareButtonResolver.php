<?php
namespace verbb\socialshare\gql\resolvers;

use verbb\socialshare\SocialShare;

use craft\gql\base\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

use Exception;

class ShareButtonResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        if (!array_key_exists('handle', $arguments)) {
            throw new Exception('You must provide a `handle` argument to query `buttons`.');
        }

        $buttons = [];

        foreach ($arguments['handle'] as $handle) {
            $buttons[] = SocialShare::$plugin->getService()->getShareButton($handle, $arguments);
        }

        return $buttons;
    }

    public static function resolveOne($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        $handle = $arguments['handle'][0] ?? null;

        if (!$handle) {
            throw new Exception('You must provide a `handle` argument to query a `button`.');
        }

        return SocialShare::$plugin->getService()->getShareButton($handle, $arguments);
    }
}
