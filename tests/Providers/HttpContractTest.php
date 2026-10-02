<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response;
use Tests\Support\HttpFake;
use verbb\socialshare\helpers\ProviderHttp;
use verbb\socialshare\providers\Facebook;

beforeEach(function(): void {
    HttpFake::reset();
});

afterEach(function(): void {
    HttpFake::reset();
});

it('applies bounded connection and request timeouts', function(): void {
    HttpFake::queue(new Response(200, [], '{}'));

    ProviderHttp::createClient()->get('https://provider.example.test/count');

    expect(HttpFake::$requests)->toHaveCount(1)
        ->and(HttpFake::$options[0]['connect_timeout'])->toBe(3)
        ->and(HttpFake::$options[0]['timeout'])->toBe(5);
});

it('reuses a cached Facebook app token across share count requests', function(): void {
    HttpFake::queue(
        new Response(200, [], '{"access_token":"test-app-token"}'),
        new Response(200, [], '{"engagement":{"share_count":2,"reaction_count":3,"comment_count":4}}'),
        new Response(200, [], '{"engagement":{"share_count":5,"reaction_count":0,"comment_count":1}}'),
    );
    $provider = new Facebook([
        'clientId' => 'test-client',
        'clientSecret' => 'test-secret',
    ]);

    expect($provider->getSharesCount('https://example.test/one'))->toBe(9)
        ->and($provider->getSharesCount('https://example.test/two'))->toBe(6)
        ->and(HttpFake::$requests)->toHaveCount(3)
        ->and((string)HttpFake::$requests[0]->getUri())->toContain('/oauth/access_token')
        ->and((string)HttpFake::$requests[1]->getUri())->toContain('graph.facebook.com')
        ->and((string)HttpFake::$requests[2]->getUri())->toContain('graph.facebook.com');
});
