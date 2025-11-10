<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

final readonly class ChainMiddleware implements MiddlewareInterface, RequestHandlerInterface {
    /**
     * @param App<null> $app
     */
    public static function empty(App $app): self {
        return new self(middleware: new IdentityMiddleware(), handler: $app);
    }

    public function __construct(
        private MiddlewareInterface $middleware,
        private RequestHandlerInterface $handler,
    ) {
    }

    public function wrap(MiddlewareInterface $middleware): self {
        $previousHandler = $this->handler;

        if ($previousHandler instanceof self) {
            return new self(
                middleware: $this->middleware,
                handler: $previousHandler->wrap($middleware),
            );
        }

        if ($this->middleware instanceof IdentityMiddleware) {
            // we can drop identity
            return new self(
                middleware: $middleware,
                handler: $this->handler,
            );
        }

        return new self(
            middleware: $this->middleware,
            handler: new self($middleware, $this->handler),
        );
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        return $this->process($request, $this->handler);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        return $this->middleware->process($request, $handler);
    }
}
