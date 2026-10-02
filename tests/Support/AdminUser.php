<?php

declare(strict_types=1);

namespace Tests\Support;

use Craft;
use craft\elements\User;
use RuntimeException;

final class AdminUser
{
    public static function login(): User
    {
        $admin = User::find()->admin(true)->status(null)->one();

        if (!$admin) {
            throw new RuntimeException('No admin user found in the test database.');
        }

        Craft::$app->getUser()->setIdentity($admin);

        return $admin;
    }
}
