<?php
namespace verbb\socialshare\gql\arguments;

use craft\gql\base\Arguments;

use GraphQL\Type\Definition\Type;

class ShareButtonArguments extends Arguments
{
    // Static Methods
    // =========================================================================

    public static function getArguments(): array
    {
        return [
            'handle' => [
                'name' => 'handle',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the button provider’s handle.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'Provide the URL to be shared.',
            ],
            'text' => [
                'name' => 'text',
                'type' => Type::string(),
                'description' => 'Provide the text to be shared.',
            ],
            'params' => [
                'name' => 'params',
                'type' => Type::listOf(Type::string()),
                'description' => 'Provide any other params to be included in the share URL.',
            ],
        ];
    }
}
