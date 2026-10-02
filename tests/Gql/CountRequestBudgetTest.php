<?php

declare(strict_types=1);

use craft\helpers\Gql;
use Tests\Support\CountingProvider;
use Tests\Support\StubProviders;
use verbb\socialshare\services\Service;
use verbb\socialshare\SocialShare;

beforeEach(function(): void {
    $this->provider = new CountingProvider();
    SocialShare::$plugin->set('providers', new StubProviders($this->provider));
    SocialShare::$plugin->set('service', new Service());

    $settings = SocialShare::$plugin->getSettings();
    $settings->enableCache = true;
    $settings->cacheDuration = 3600;
    $settings->friendlyCount = false;
    $settings->graphqlProviderRequestLimit = 2;
    $settings->graphqlProviderRequestWindow = 60;
});

afterEach(function(): void {
    Craft::$app->getGql()->setActiveSchema(null);
});

it('caches repeated GraphQL share queries and limits distinct outbound requests', function(): void {
    $schema = Gql::createFullAccessSchema();
    $query = 'query($url: String!) { socialShare { shares(handle: "counting", url: $url, friendlyCount: false) } }';
    $aliasQuery = 'query($url: String!) { socialShare { cached: shares(handle: "counting", url: $url, friendlyCount: false) } }';

    $first = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/one']);
    $repeat = Craft::$app->getGql()->executeQuery($schema, $aliasQuery, ['url' => 'https://example.test/one']);
    $second = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/two']);
    $blocked = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/three']);

    expect($first['errors'] ?? [])->toBe([])
        ->and($repeat['errors'] ?? [])->toBe([])
        ->and($second['errors'] ?? [])->toBe([])
        ->and($blocked['errors'] ?? [])->toBe([])
        ->and($first['data']['socialShare']['shares'])->toBe('7')
        ->and($repeat['data']['socialShare']['cached'])->toBe('7')
        ->and($second['data']['socialShare']['shares'])->toBe('7')
        ->and($blocked['data']['socialShare']['shares'])->toBeNull()
        ->and($this->provider->shareRequests)->toBe(2);
});

it('caches repeated GraphQL follower queries and limits distinct outbound requests', function(): void {
    $schema = Gql::createFullAccessSchema();
    $query = 'query($account: String!) { socialShare { followers(handle: "counting", account: $account, friendlyCount: false) } }';

    $first = Craft::$app->getGql()->executeQuery($schema, $query, ['account' => 'first']);
    $repeat = Craft::$app->getGql()->executeQuery($schema, $query, ['account' => 'first']);
    $second = Craft::$app->getGql()->executeQuery($schema, $query, ['account' => 'second']);
    $blocked = Craft::$app->getGql()->executeQuery($schema, $query, ['account' => 'third']);

    expect($first['errors'] ?? [])->toBe([])
        ->and($repeat['errors'] ?? [])->toBe([])
        ->and($second['errors'] ?? [])->toBe([])
        ->and($blocked['errors'] ?? [])->toBe([])
        ->and($first['data']['socialShare']['followers'])->toBe('13')
        ->and($repeat['data']['socialShare']['followers'])->toBe('13')
        ->and($second['data']['socialShare']['followers'])->toBe('13')
        ->and($blocked['data']['socialShare']['followers'])->toBeNull()
        ->and($this->provider->followerRequests)->toBe(2);
});
