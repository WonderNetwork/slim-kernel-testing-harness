<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\SlimKernelHttpClient;

final readonly class CustomerUserAssertion {
    public function __construct(
        private string $id,
        private string $name,
        private SlimKernelHttpClient $httpClient,
    ) {
    }

    public function delete(): void {
        $this->httpClient->delete("/users/$this->id");
    }
}
