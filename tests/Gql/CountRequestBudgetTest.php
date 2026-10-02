<?php

declare(strict_types=1);

use craft\helpers\Gql;
use Tests\Support\CountingProvider;
use Tests\Support\FixedAccountProvider;
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
    $settings->graphqlNegativeCacheDuration = 60;
    $settings->graphqlOperationRequestLimit = 10;
    $settings->graphqlAggregateRequestLimit = 10;
    $settings->graphqlProviderRequestLimit = 10;
    $settings->graphqlProviderRequestWindow = 60;
});

afterEach(function(): void {
    Craft::$app->getGql()->setActiveSchema(null);
});

it('caches repeated GraphQL share queries and limits distinct outbound requests', function(): void {
    SocialShare::$plugin->getSettings()->graphqlProviderRequestLimit = 2;

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
    SocialShare::$plugin->getSettings()->graphqlProviderRequestLimit = 2;

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

it('limits distinct count misses within one GraphQL operation', function(): void {
    SocialShare::$plugin->getSettings()->graphqlOperationRequestLimit = 2;

    $schema = Gql::createFullAccessSchema();
    $query = <<<'GRAPHQL'
        query {
            socialShare {
                first: shares(handle: "counting", url: "https://example.test/one", friendlyCount: false)
                second: shares(handle: "counting", url: "https://example.test/two", friendlyCount: false)
                blocked: shares(handle: "counting", url: "https://example.test/three", friendlyCount: false)
            }
        }
        GRAPHQL;

    $result = Craft::$app->getGql()->executeQuery($schema, $query);

    expect($result['errors'] ?? [])->toBe([])
        ->and($result['data']['socialShare']['first'])->toBe('7')
        ->and($result['data']['socialShare']['second'])->toBe('7')
        ->and($result['data']['socialShare']['blocked'])->toBeNull()
        ->and($this->provider->shareRequests)->toBe(2);
});

it('gives each GraphQL operation a fresh count budget', function(): void {
    SocialShare::$plugin->getSettings()->graphqlOperationRequestLimit = 1;

    $schema = Gql::createFullAccessSchema();
    $query = 'query($url: String!) { socialShare { shares(handle: "counting", url: $url, friendlyCount: false) } }';

    $first = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/one']);
    $second = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/two']);

    expect($first['data']['socialShare']['shares'])->toBe('7')
        ->and($second['data']['socialShare']['shares'])->toBe('7')
        ->and($this->provider->shareRequests)->toBe(2);
});

it('limits aggregate GraphQL count misses across operations', function(): void {
    SocialShare::$plugin->getSettings()->graphqlAggregateRequestLimit = 2;

    $schema = Gql::createFullAccessSchema();
    $query = 'query($url: String!) { socialShare { shares(handle: "counting", url: $url, friendlyCount: false) } }';

    $first = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/one']);
    $second = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/two']);
    $blocked = Craft::$app->getGql()->executeQuery($schema, $query, ['url' => 'https://example.test/three']);

    expect($first['data']['socialShare']['shares'])->toBe('7')
        ->and($second['data']['socialShare']['shares'])->toBe('7')
        ->and($blocked['data']['socialShare']['shares'])->toBeNull()
        ->and($this->provider->shareRequests)->toBe(2);
});

it('rejects oversized GraphQL count inputs before cache or provider work', function(): void {
    $schema = Gql::createFullAccessSchema();
    $followersQuery = 'query($account: String!) { socialShare { followers(handle: "counting", account: $account, friendlyCount: false) } }';
    $sharesQuery = 'query($url: String!) { socialShare { shares(handle: "counting", url: $url, friendlyCount: false) } }';

    $acceptedAccount = Craft::$app->getGql()->executeQuery($schema, $followersQuery, [
        'account' => str_repeat('a', Service::MAX_GRAPHQL_ACCOUNT_LENGTH),
    ]);
    $blockedAccount = Craft::$app->getGql()->executeQuery($schema, $followersQuery, [
        'account' => str_repeat('a', Service::MAX_GRAPHQL_ACCOUNT_LENGTH + 1),
    ]);
    $acceptedUrl = Craft::$app->getGql()->executeQuery($schema, $sharesQuery, [
        'url' => str_pad('https://example.test/', Service::MAX_GRAPHQL_URL_LENGTH, 'a'),
    ]);
    $blockedUrl = Craft::$app->getGql()->executeQuery($schema, $sharesQuery, [
        'url' => str_pad('https://example.test/', Service::MAX_GRAPHQL_URL_LENGTH + 1, 'a'),
    ]);

    expect($acceptedAccount['data']['socialShare']['followers'])->toBe('13')
        ->and($blockedAccount['data']['socialShare']['followers'])->toBeNull()
        ->and($acceptedUrl['data']['socialShare']['shares'])->toBe('7')
        ->and($blockedUrl['data']['socialShare']['shares'])->toBeNull()
        ->and($this->provider->followerRequests)->toBe(1)
        ->and($this->provider->shareRequests)->toBe(1);
});

it('uses one cache identity for connected-account providers', function(): void {
    $provider = new FixedAccountProvider();
    SocialShare::$plugin->set('providers', new StubProviders($provider));

    $schema = Gql::createFullAccessSchema();
    $query = 'query($account: String!) { socialShare { followers(handle: "fixed-account", account: $account, friendlyCount: false) } }';
    $aliasQuery = 'query($account: String!) { socialShare { cached: followers(handle: "fixed-account", account: $account, friendlyCount: false) } }';

    $first = Craft::$app->getGql()->executeQuery($schema, $query, ['account' => 'ignored-one']);
    $second = Craft::$app->getGql()->executeQuery($schema, $aliasQuery, ['account' => 'ignored-two']);

    expect($first['data']['socialShare']['followers'])->toBe('5')
        ->and($second['data']['socialShare']['cached'])->toBe('5')
        ->and($provider->followerRequests)->toBe(1);
});

it('expires non-positive GraphQL count results quickly', function(?int $initialResult, ?string $expectedResult): void {
    SocialShare::$plugin->getSettings()->graphqlNegativeCacheDuration = 1;
    $this->provider->shareResult = $initialResult;

    $schema = Gql::createFullAccessSchema();
    $query = 'query { socialShare { shares(handle: "counting", url: "https://example.test/non-positive", friendlyCount: false) } }';
    $aliasQuery = 'query { socialShare { cached: shares(handle: "counting", url: "https://example.test/non-positive", friendlyCount: false) } }';

    $first = Craft::$app->getGql()->executeQuery($schema, $query);
    $cacheHit = Craft::$app->getGql()->executeQuery($schema, $aliasQuery);
    $this->provider->shareResult = 9;
    sleep(2);
    $refreshed = Craft::$app->getGql()->executeQuery($schema, $aliasQuery);

    expect($first['data']['socialShare']['shares'])->toBe($expectedResult)
        ->and($cacheHit['data']['socialShare']['cached'])->toBe($expectedResult)
        ->and($refreshed['data']['socialShare']['cached'])->toBe('9')
        ->and($this->provider->shareRequests)->toBe(2);
})->with([
    'zero' => [0, '0'],
    'null' => [null, null],
]);

it('expires budget-denied GraphQL results with the request window', function(): void {
    $settings = SocialShare::$plugin->getSettings();
    $settings->graphqlAggregateRequestLimit = 1;
    $settings->graphqlNegativeCacheDuration = 60;
    $settings->graphqlProviderRequestWindow = 1;

    $schema = Gql::createFullAccessSchema();
    $allowedQuery = 'query { socialShare { shares(handle: "counting", url: "https://example.test/allowed", friendlyCount: false) } }';
    $blockedQuery = 'query { socialShare { blocked: shares(handle: "counting", url: "https://example.test/blocked", friendlyCount: false) } }';

    $allowed = Craft::$app->getGql()->executeQuery($schema, $allowedQuery);
    $blocked = Craft::$app->getGql()->executeQuery($schema, $blockedQuery);
    sleep(2);
    $retried = Craft::$app->getGql()->executeQuery($schema, $blockedQuery);

    expect($allowed['data']['socialShare']['shares'])->toBe('7')
        ->and($blocked['data']['socialShare']['blocked'])->toBeNull()
        ->and($retried['data']['socialShare']['blocked'])->toBe('7')
        ->and($this->provider->shareRequests)->toBe(2);
});
