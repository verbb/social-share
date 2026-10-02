<?php
namespace verbb\socialshare\helpers;

use Craft;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use LengthException;

class ProviderHttp
{
    // Constants
    // =========================================================================

    public const MAX_RESPONSE_BYTES = 1_048_576;


    // Static Methods
    // =========================================================================

    public static function createClient(array $config = []): Client
    {
        $client = Craft::createGuzzleClient(self::_withTimeoutDefaults($config));

        return self::_withResponseLimit($client);
    }

    public static function withTimeouts(Client $client): Client
    {
        $client = new Client(self::_withTimeoutDefaults($client->getConfig()));

        return self::_withResponseLimit($client);
    }

    private static function _withTimeoutDefaults(array $config): array
    {
        $siteConfig = Craft::$app->getConfig()->getConfigFromFile('guzzle');

        // Site and provider-specific policies remain authoritative over these safe defaults.
        foreach (['connect_timeout' => 3, 'timeout' => 5] as $option => $default) {
            if (!array_key_exists($option, $siteConfig) && !array_key_exists($option, $config)) {
                $config[$option] = $default;
            }
        }

        return $config;
    }

    private static function _withResponseLimit(Client $client): Client
    {
        $config = $client->getConfig();
        $handler = $config['handler'];

        if ($handler instanceof HandlerStack) {
            $handler = clone $handler;
        } else {
            // A raw custom handler already owns its middleware policy, so add only this boundary.
            $handler = new HandlerStack($handler);
        }

        $handler->push(self::_responseLimitMiddleware(), 'social-share-response-limit');
        $config['handler'] = $handler;

        return new Client($config);
    }

    private static function _responseLimitMiddleware(): callable
    {
        return static function(callable $handler): callable {
            return static function(RequestInterface $request, array $options) use ($handler): PromiseInterface {
                $onHeaders = $options['on_headers'] ?? null;
                $options['on_headers'] = static function(ResponseInterface $response) use ($onHeaders): void {
                    self::_assertResponseSize($response);

                    if ($onHeaders !== null) {
                        $onHeaders($response);
                    }
                };

                // A fresh bounded sink stops chunked, decompressed and falsely declared bodies during transfer.
                $options['stream'] = false;
                $options['sink'] = new LimitedResponseStream(self::_responseSink($options['sink'] ?? null), self::MAX_RESPONSE_BYTES);

                return $handler($request, $options)->then(static function(ResponseInterface $response): ResponseInterface {
                    self::_assertResponseSize($response);

                    if (!$response->getBody() instanceof LimitedResponseStream) {
                        $response = $response->withBody(new LimitedResponseStream($response->getBody(), self::MAX_RESPONSE_BYTES));
                    }

                    return $response;
                });
            };
        };
    }

    private static function _responseSink(mixed $sink): StreamInterface
    {
        if ($sink === null) {
            $sink = Utils::tryFopen('php://temp', 'w+');
        }

        if (is_string($sink)) {
            return new LazyOpenStream($sink, 'w+');
        }

        return Utils::streamFor($sink);
    }

    private static function _assertResponseSize(ResponseInterface $response): void
    {
        foreach ($response->getHeader('Content-Length') as $headerValue) {
            foreach (explode(',', $headerValue) as $length) {
                $length = trim($length);

                if ($length !== '' && ctype_digit($length) && (int)$length > self::MAX_RESPONSE_BYTES) {
                    throw new LengthException(sprintf('Provider response exceeded the %d-byte limit.', self::MAX_RESPONSE_BYTES));
                }
            }
        }

        $bodySize = $response->getBody()->getSize();

        if ($bodySize !== null && $bodySize > self::MAX_RESPONSE_BYTES) {
            throw new LengthException(sprintf('Provider response exceeded the %d-byte limit.', self::MAX_RESPONSE_BYTES));
        }
    }
}
