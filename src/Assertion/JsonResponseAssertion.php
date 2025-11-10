<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Assertion;

use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\HttpResponseAssertion;

/**
 * Represents a HttpResponse of a resource endpoint
 */
trait JsonResponseAssertion {
    public static function ofJsonResponse(HttpResponseAssertion $response): static {
        return new static($response->expectJson(), $response);
    }

    public function __construct(
        protected readonly array $data,
        protected readonly HttpResponseAssertion $response,
    ) {
    }
}
