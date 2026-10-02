<?php

declare(strict_types=1);

namespace Tests\Support;

use Craft;
use craft\elements\User;

final class NonAdminUser
{
    public static function login(): User
    {
        $user = new class extends User {
            public function can($permission): bool
            {
                return false;
            }
        };
        $user->username = 'test-editor';
        $user->email = 'test-editor@example.test';
        $user->admin = false;
        Craft::$app->getUser()->setIdentity($user);

        return $user;
    }
}
