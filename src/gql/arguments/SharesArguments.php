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
                'description' => 'Whether to enable the cache for results.',
            ],
            'cacheDuration' => [
                'name' => 'cacheDuration',
                'type' => Type::int(),
                'description' => 'The number of seconds to cache results for.',
            ],
        ];
    }
}
