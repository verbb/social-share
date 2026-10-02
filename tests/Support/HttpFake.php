<?php

declare(strict_types=1);

namespace Tests\Support;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\Create;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class HttpFake
{
    public static array $options = [];
    public static array $requests = [];

    private static array $responses = [];

    public static function reset(): void
    {
        self::$options = [];
        self::$requests = [];
        self::$responses = [];
    }

    public static function queue(ResponseInterface|Throwable ...$responses): void
    {
        array_push(self::$responses, ...$responses);
    }

    public static function handler(): callable
    {
        return static function(RequestInterface $request, array $options) {
            self::$requests[] = $request;
            self::$options[] = $options;

            if (self::$responses === []) {
                return Create::rejectionFor(new ConnectException('Unexpected outbound request.', $request));
            }

            $response = array_shift(self::$responses);

            return $response instanceof Throwable ? Create::rejectionFor($response) : Create::promiseFor($response);
        };
    }
}
