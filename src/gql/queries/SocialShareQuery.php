<?php
namespace verbb\socialshare\gql\queries;

use verbb\socialshare\gql\arguments\SocialShareArguments;
use verbb\socialshare\gql\interfaces\SocialShareInterface;
use verbb\socialshare\gql\resolvers\SocialShareResolver;
use verbb\socialshare\helpers\Gql as GqlHelper;

use craft\gql\base\Query;

class SocialShareQuery extends Query
{
    // Static Methods
    // =========================================================================

    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQuerySocialShare()) {
            return [];
        }

        return [
            'socialShare' => [
                'type' => SocialShareInterface::getType(),
                'args' => SocialShareArguments::getArguments(),
                'resolve' => SocialShareResolver::class . '::resolve',
                'description' => 'This query is used to query for Social Share content.'
            ],
        ];
    }
}
