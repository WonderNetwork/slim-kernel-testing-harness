<?php

declare(strict_types=1);

namespace Acme\Example\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Assume you have a very basic authentication mechanism:
 * Based on the user’s session, the userId is attached to
 * the request, and in addition the `$_SESSION['admin'] flag
 * is set based on the user’s permissions.
 *
 * The above logic is implemented as a middleware in your app.
 *
 * Add the following middleware to the SlimKernelHttpClient
 * to bypass that logic in a way — by attaching the required
 * authentication/authorization flags to the request, rather
 * than expecting the app logic to do so.
 */
final readonly class AdminSessionMiddleware implements MiddlewareInterface {
    public function __construct(private int $userId) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $_SESSION['admin'] = true;
        try {
            return $handler->handle(
                $request->withAttribute('userId', $this->userId)
            );
        } finally {
            unset($_SESSION['admin']);
        }
    }
}
