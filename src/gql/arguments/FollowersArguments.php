<?php
namespace verbb\socialshare\gql\arguments;

use craft\gql\base\Arguments;

use GraphQL\Type\Definition\Type;

class FollowersArguments extends Arguments
{
    // Static Methods
    // =========================================================================

    public static function getArguments(): array
    {
        return [
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'Narrows the query results based on the followers provider’s handle.',
            ],
            'account' => [
                'name' => 'account',
                'type' => Type::string(),
                'description' => 'Narrows the query results based on the account to check followers for.',
            ],
            'friendlyCount' => [
                'name' => 'friendlyCount',
                'type' => Type::boolean(),
                'description' => 'Whether the returned count should be a "friendly" number.',
            ],
            'enableCache' => [
                'name' => 'enableCache',
                'type' => Type::boolean(),
                'description' => 'Retained for query compatibility but ignored. The site’s Social Share settings control result caching.',
            ],
            'cacheDuration' => [
                'name' => 'cacheDuration',
                'type' => Type::int(),
                'description' => 'Retained for query compatibility but ignored. The site’s Social Share settings control the cache duration.',
            ],
        ];
    }
}
