<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class AddToRequestPayloadParamMiddleware implements MiddlewareInterface {
    public function __construct(private string $name, private string $value) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $previousBody = $request->getParsedBody();

        return $handler->handle(
            $request
                ->withParsedBody(
                    [$this->name => trim(sprintf('%s %s', $previousBody[$this->name] ?? "", $this->value))]
                    + $previousBody,
                ),
        );
    }
}
