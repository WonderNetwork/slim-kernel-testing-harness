<?php

declare(strict_types=1);

namespace Acme\Example\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\RequestBuilder;

/**
 * Artificially simulate the client’s remote IP address
 */
final readonly class WithRemoteIpMiddleware implements MiddlewareInterface {
    public function __construct(private string $ip = '127.0.0.1') {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        return $handler->handle(
            RequestBuilder
                ::ofRequest($request)
                ->withIp($this->ip)
                ->build(),
        );
    }
}
