<?php
declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use Psr\Http\Message\ServerRequestInterface;

final class RequestMother {
    public static function get(string $uri): ServerRequestInterface {
        return RequestBuilder::of('GET', $uri)->build();
    }

    public static function head(string $uri): ServerRequestInterface {
        return RequestBuilder::of('HEAD', $uri)->build();
    }

    public static function post(string $uri, array $payload): ServerRequestInterface {
        return RequestBuilder::of('POST', $uri)->withParsedBody($payload)->build();
    }

    public static function put(string $uri, array $payload): ServerRequestInterface {
        return RequestBuilder::of('PUT', $uri)->withParsedBody($payload)->build();
    }

    public static function patch(string $uri, array $payload): ServerRequestInterface {
        return RequestBuilder::of('PATCH', $uri)->withParsedBody($payload)->build();
    }

    public static function delete(string $url): ServerRequestInterface {
        return RequestBuilder::of('DELETE', $url)->build();
    }
}
