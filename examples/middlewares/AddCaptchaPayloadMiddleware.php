<?php

declare(strict_types=1);

namespace Acme\Example\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Assuming your anonymous clients need to provide a captcha challenge
 * for sensitive requests, and that the request param uniformly requires
 * the challenge response to be present as the `captcha` payload field
 *
 * This middleware automatically attaches a valid value (let’s say that
 * your test adapter allows you to generate a valid ticket), and you can
 * even easily turn it on and off for each request to test error conditions
 */
final class AddCaptchaPayloadMiddleware implements MiddlewareInterface {
    private bool $enabled = false;

    public function __construct(private readonly \Closure $validCaptchaFactory) {
    }

    public function enable(): void {
        $this->enabled = true;
    }

    public function disable(): void {
        $this->enabled = false;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if ($this->enabled && $request->getParsedBody()) {
            $captcha = ($this->validCaptchaFactory)();
            $request = $request->withParsedBody(
                $request->getParsedBody() + compact('captcha'),
            );
        }

        return $handler->handle($request);
    }
}
