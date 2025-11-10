<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\SlimKernelHttpClient;

final readonly class CustomerAssertion {
    public function __construct(
        private string $id,
        private string $name,
        private SlimKernelHttpClient $httpClient,
    ) {
    }

    public function users(): CustomerUsersUseCase {
        return CustomerUsersUseCase::of(
            $this->httpClient->withBaseUrl("/api/v1/customers/$this->id"),
        );
    }
}
