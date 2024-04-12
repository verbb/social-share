<?php
namespace verbb\socialshare\migrations;

use craft\db\Migration;
use craft\helpers\MigrationHelper;

use verbb\auth\Auth;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        // Ensure that the Auth module kicks off setting up tables
        Auth::getInstance()->migrator->up();

        return true;
    }

    public function safeDown(): bool
    {
        // Delete all tokens for this plugin
        Auth::getInstance()->getTokens()->deleteTokensByOwner('social-share');

        return true;
    }

}
