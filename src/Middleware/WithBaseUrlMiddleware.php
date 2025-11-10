<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class WithBaseUrlMiddleware implements MiddlewareInterface {
    public function __construct(private string $baseUrl) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        return $handler->handle(
            $request->withUri(
                $request
                    ->getUri()
                    ->withPath($this->baseUrl.$request->getUri()->getPath()),
            ),
        );
    }
}
