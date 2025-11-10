<?php

declare(strict_types=1);

namespace Acme\Example\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\RequestBuilder;

/**
 * There’s nothing simpler than this one:
 * Adding this to the request will make the client appear as if they
 * used basic auth on their request.
 *
 * Notice that `RequestBuilder::ofRequest()` is used, since the PSR-7
 * ServerRequestInterface does not have a `withServerParams()` method
 */
final readonly class WithBasicAuthMiddleware implements MiddlewareInterface {
    public function __construct(private string $username, private string $password) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        return $handler->handle(
            RequestBuilder::ofRequest($request)
                ->withServerParam('PHP_AUTH_USER', $this->username)
                ->withServerParam('PHP_AUTH_PW', $this->password)
                ->build(),
        );
    }
}
