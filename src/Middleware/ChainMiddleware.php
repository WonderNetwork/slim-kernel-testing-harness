<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class ChainMiddleware implements RequestHandlerInterface {
    public static function empty(RequestHandlerInterface $app): self {
        return new self($app);
    }

    /**
     * @param list<MiddlewareInterface> $middlewares
     */
    private function __construct(
        private RequestHandlerInterface $handler,
        private array $middlewares = [],
    ) {
    }

    public function wrap(MiddlewareInterface $middleware): self {
        return new self(
            handler: $this->handler,
            middlewares: [$middleware, ...$this->middlewares],
        );
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $handler = array_reduce(
            $this->middlewares,
            MiddlewareDelegator::wrapMiddleware(...),
            $this->handler,
        );

        return $handler->handle($request);
    }
}
