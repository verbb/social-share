<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\PumpStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransportSharing;
use Psr\Http\Message\RequestInterface;
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
        ->and(HttpFake::$options[0]['timeout'])->toBe(5)
        ->and(HttpFake::$options[0]['stream'])->toBeFalse();
});

it('accepts provider responses at the byte limit', function(): void {
    $body = str_repeat('a', ProviderHttp::MAX_RESPONSE_BYTES);
    HttpFake::queue(new Response(200, [], $body));

    $response = ProviderHttp::createClient()->get('https://provider.example.test/count');

    expect((string)$response->getBody())->toBe($body);
});

it('rejects declared oversized provider responses', function(): void {
    HttpFake::queue(new Response(200, [
        'Content-Length' => (string)(ProviderHttp::MAX_RESPONSE_BYTES + 1),
    ], '{}'));

    expect(fn() => ProviderHttp::createClient()->get('https://provider.example.test/count'))
        ->toThrow(LengthException::class);
});

it('rejects oversized provider responses without trusting their length header', function(?string $contentLength): void {
    $headers = $contentLength === null ? [] : ['Content-Length' => $contentLength];
    HttpFake::queue(new Response(200, $headers, str_repeat('a', ProviderHttp::MAX_RESPONSE_BYTES + 1)));

    expect(fn() => ProviderHttp::createClient()->get('https://provider.example.test/count'))
        ->toThrow(LengthException::class);
})->with([
    'missing length' => [null],
    'falsely small length' => ['2'],
]);

it('rejects oversized unknown-length bodies returned by custom handlers', function(): void {
    $remaining = ProviderHttp::MAX_RESPONSE_BYTES + 1;
    $body = new PumpStream(static function(int $length) use (&$remaining): string|false {
        if ($remaining === 0) {
            return false;
        }

        $chunkLength = min($length, $remaining);
        $remaining -= $chunkLength;

        return str_repeat('a', $chunkLength);
    });
    HttpFake::queue(new Response(200, [], $body));
    $response = ProviderHttp::createClient()->get('https://provider.example.test/count');

    expect(fn() => (string)$response->getBody())->toThrow(LengthException::class);
});

it('stops oversized provider responses while the handler writes them', function(): void {
    $handler = static function(RequestInterface $request, array $options) {
        $response = new Response();
        $options['on_headers']($response);
        $options['sink']->write(str_repeat('a', ProviderHttp::MAX_RESPONSE_BYTES));
        $options['sink']->write('b');

        return Create::promiseFor($response->withBody($options['sink']));
    };

    expect(fn() => ProviderHttp::createClient(['handler' => $handler])->get('https://provider.example.test/count'))
        ->toThrow(LengthException::class);
});

it('uses a fresh bounded response sink for every request', function(): void {
    $requestNumber = 0;
    $handler = static function(RequestInterface $request, array $options) use (&$requestNumber) {
        $requestNumber++;
        $response = new Response();
        $options['on_headers']($response);
        $options['sink']->write("response-$requestNumber");
        $options['sink']->rewind();

        return Create::promiseFor($response->withBody($options['sink']));
    };
    $client = ProviderHttp::createClient(['handler' => $handler]);
    $headersObserved = 0;

    $first = $client->get('https://provider.example.test/first', [
        'on_headers' => static function() use (&$headersObserved): void {
            $headersObserved++;
        },
    ]);
    $second = $client->get('https://provider.example.test/second');

    expect((string)$first->getBody())->toBe('response-1')
        ->and((string)$second->getBody())->toBe('response-2')
        ->and($headersObserved)->toBe(1);
});

it('applies the response limit to wrapped OAuth clients', function(): void {
    HttpFake::queue(new Response(200, [], str_repeat('a', ProviderHttp::MAX_RESPONSE_BYTES + 1)));
    $client = ProviderHttp::withTimeouts(Craft::createGuzzleClient());

    expect(fn() => $client->get('https://provider.example.test/count'))
        ->toThrow(LengthException::class);
});

it('preserves Guzzle-managed handler policies when wrapping clients', function(array $config): void {
    $client = ProviderHttp::withTimeouts(new Client($config));

    expect($client)->toBeInstanceOf(Client::class);
})->with([
    'host connection cap' => [['max_host_connections' => 2]],
    'total connection cap' => [['max_total_connections' => 2]],
    'required transport sharing' => [['transport_sharing' => TransportSharing::HANDLER_REQUIRE]],
]);

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
