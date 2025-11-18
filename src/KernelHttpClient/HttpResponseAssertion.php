<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

final readonly class HttpResponseAssertion {
    public static function of(ResponseInterface $response, SlimKernelHttpClient $httpClient): self {
        return new self($response, $httpClient);
    }

    public function __construct(
        public ResponseInterface $response,
        public SlimKernelHttpClient $httpClient,
    ) {
    }

    public function assertSuccess(): self {
        if (false === $this->isSuccessful()) {
            $errorPage = $this->slimErrorPage();
            Assert::assertLessThan(
                maximum: StatusCodeInterface::STATUS_BAD_REQUEST,
                actual: $this->response->getStatusCode(),
                message: sprintf(
                    "Request did not complete successfully. Error message: '%s' of '%s'",
                    $errorPage->message,
                    $errorPage->type,
                ),
            );
        }

        return $this;
    }

    public function assertFailure(): self {
        Assert::assertGreaterThanOrEqual(
            minimum: StatusCodeInterface::STATUS_BAD_REQUEST,
            actual: $this->response->getStatusCode(),
        );

        return $this;
    }

    public function expectJson(): array {
        $json = (string) $this->response->getBody();
        Assert::assertJson($json, message: "Response body was not a proper JSON.");

        $data = json_decode($json, associative: true);
        Assert::assertIsArray($data, message: "Response body was not an array.");

        return $data;
    }

    public function expectHtml(): string {
        return (string) $this->response->getBody();
    }

    public function assertRedirect(string $to = null): self {
        Assert::assertContains(
            needle: $this->response->getStatusCode(),
            haystack: [
                StatusCodeInterface::STATUS_MOVED_PERMANENTLY,
                StatusCodeInterface::STATUS_FOUND,
                StatusCodeInterface::STATUS_SEE_OTHER,
                StatusCodeInterface::STATUS_PERMANENT_REDIRECT,
                StatusCodeInterface::STATUS_TEMPORARY_REDIRECT,
            ],
            message: "The http response is not a redirect.",
        );

        if ($to) {
            Assert::assertSame(
                expected: $to,
                actual: $this->response->getHeaderLine('Location'),
                message: "The response does not redirect to the desired location.",
            );
        }

        return $this;
    }

    public function assertBadRequest(): self {
        Assert::assertSame(
            expected: $this->response->getStatusCode(),
            actual: StatusCodeInterface::STATUS_BAD_REQUEST,
        );

        return $this;
    }

    public function assertForbidden(): self {
        Assert::assertSame(
            expected: $this->response->getStatusCode(),
            actual: StatusCodeInterface::STATUS_FORBIDDEN,
        );

        return $this;
    }

    public function assertThrottled(): self {
        Assert::assertSame(
            expected: $this->response->getStatusCode(),
            actual: StatusCodeInterface::STATUS_TOO_MANY_REQUESTS,
        );

        return $this;
    }

    public function slimErrorPage(): SlimErrorResponseAssertion {
        return SlimErrorResponseParser::fromResponse($this);
    }

    public function isSuccessful(): bool {
        return $this->response->getStatusCode() < StatusCodeInterface::STATUS_BAD_REQUEST;
    }
}
