<?php
namespace verbb\socialshare\gql\arguments;

use craft\gql\base\Arguments;

use GraphQL\Type\Definition\Type;

class ButtonArguments extends Arguments
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
        ];
    }
}
