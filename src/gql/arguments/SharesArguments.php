<?php
namespace verbb\socialshare\gql\arguments;

use craft\gql\base\Arguments;

use GraphQL\Type\Definition\Type;

class SharesArguments extends Arguments
{
    // Static Methods
    // =========================================================================

    public static function getArguments(): array
    {
        return [
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'Narrows the query results based on the shares provider’s handle.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'Narrows the query results based on the URL to check shares for.',
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
