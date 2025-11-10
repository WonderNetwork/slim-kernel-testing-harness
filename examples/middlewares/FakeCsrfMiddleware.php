<?php
declare(strict_types=1);

namespace Acme\Example\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Assuming you delegated validating CSRF tokens to a middleware,
 * adding this middleware to the SlimKernelHttpClient to artificially
 * set the expected csrf attribute on a request, bypassing that
 * security feature in testing
 */
final class FakeCsrfMiddleware implements MiddlewareInterface {
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        return $handler->handle(
            $request->withAttribute('csrf-token-valid', true),
        );
    }
}
