<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class MiddlewareDelegator implements RequestHandlerInterface {
    public static function wrapMiddleware(RequestHandlerInterface $handler, MiddlewareInterface $middleware): self {
        return new self($handler, $middleware);
    }

    private function __construct(
        private RequestHandlerInterface $next,
        private MiddlewareInterface $middleware,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        return $this->middleware->process($request, $this->next);
    }
}
