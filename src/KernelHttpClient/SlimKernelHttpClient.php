<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\App;
use WonderNetwork\SlimKernelTestingHarness\Middleware\AjaxRequestMiddleware;
use WonderNetwork\SlimKernelTestingHarness\Middleware\ChainMiddleware;
use WonderNetwork\SlimKernelTestingHarness\Middleware\JsonRequestMiddleware;
use WonderNetwork\SlimKernelTestingHarness\Middleware\WithBaseUrlMiddleware;

final readonly class SlimKernelHttpClient {
    /**
     * @param App<null> $app
     */
    public static function create(App $app): self {
        return new self(
            handler: ChainMiddleware::empty($app),
            responseExpectation: ResponseExpectation::Success,
        );
    }

    private function __construct(
        private ChainMiddleware $handler,
        private ResponseExpectation $responseExpectation,
    ) {
    }

    public function withResponseExpectation(ResponseExpectation $responseExpectation): self {
        return new self(
            handler: $this->handler,
            responseExpectation: $responseExpectation,
        );
    }

    public function withMiddleware(MiddlewareInterface $middleware): self {
        return new self(
            handler: $this->handler->wrap($middleware),
            responseExpectation: $this->responseExpectation,
        );
    }

    public function withBaseUrl(string $baseUrl): self {
        return $this->withMiddleware(new WithBaseUrlMiddleware($baseUrl));
    }

    public function json(): self {
        return $this
            ->withMiddleware(new JsonRequestMiddleware())
            ->withMiddleware(new AjaxRequestMiddleware());
    }

    public function get(string $url): HttpResponseAssertion {
        return $this->request(RequestMother::get($url));
    }

    public function head(string $url): HttpResponseAssertion {
        return $this->request(RequestMother::head($url));
    }

    public function post(string $url, array $payload = []): HttpResponseAssertion {
        return $this->request(RequestMother::post($url, $payload));
    }

    public function patch(string $url, array $payload = []): HttpResponseAssertion {
        return $this->request(RequestMother::patch($url, $payload));
    }

    public function put(string $url, array $payload = []): HttpResponseAssertion {
        return $this->request(RequestMother::put($url, $payload));
    }

    public function delete(string $url): HttpResponseAssertion {
        return $this->request(RequestMother::delete($url));
    }

    public function request(ServerRequestInterface $request): HttpResponseAssertion {
        $response = HttpResponseAssertion::of(
            response: $this->handler->handle($request),
            httpClient: $this,
        );

        return match ($this->responseExpectation) {
            ResponseExpectation::Failure => $response->assertFailure(),
            ResponseExpectation::Success => $response->assertSuccess(),
            ResponseExpectation::None => $response,
        };
    }
}
