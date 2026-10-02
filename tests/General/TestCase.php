<?php

declare(strict_types=1);

namespace Tests\General;

use Craft;
use PHPUnit\Framework\TestCase as BaseTestCase;
use RuntimeException;
use verbb\socialshare\SocialShare;

abstract class TestCase extends BaseTestCase
{
    private mixed $previousProviders = null;
    private mixed $previousRequest = null;
    private mixed $previousResponse = null;
    private mixed $previousService = null;
    private mixed $previousUser = null;
    private mixed $previousIdentity = null;
    private array $previousServer = [];
    private array $settings = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(Craft::class) || !Craft::$app || !SocialShare::$plugin) {
            throw new RuntimeException('Craft and Social Share must be bootstrapped before running integration tests.');
        }

        $this->previousProviders = SocialShare::$plugin->get('providers', false);
        $this->previousService = SocialShare::$plugin->get('service', false);
        $this->previousRequest = Craft::$app->get('request', false);
        $this->previousResponse = Craft::$app->get('response', false);
        $this->previousUser = Craft::$app->get('user', false);
        $this->previousIdentity = Craft::$app->getUser()->getIdentity();
        $this->previousServer = $_SERVER;
        $this->settings = SocialShare::$plugin->getSettings()->getAttributes();

        Craft::$app->getCache()->flush();
        Craft::$app->getGql()->flushCaches();
    }

    protected function tearDown(): void
    {
        try {
            SocialShare::$plugin->set('providers', $this->previousProviders);
            SocialShare::$plugin->set('service', $this->previousService);
            SocialShare::$plugin->getSettings()->setAttributes($this->settings, false);
            Craft::$app->set('request', $this->previousRequest);
            Craft::$app->set('response', $this->previousResponse);
            Craft::$app->set('user', $this->previousUser);
            Craft::$app->getUser()->setIdentity($this->previousIdentity);
            $_SERVER = $this->previousServer;
            Craft::$app->getCache()->flush();
            Craft::$app->getGql()->flushCaches();
        } finally {
            parent::tearDown();
        }
    }
}
