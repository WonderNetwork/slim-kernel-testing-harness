<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use WonderNetwork\SlimKernelTestingHarness\UseCase\KernelHttpClientUseCase;

final readonly class CustomersUseCasesFacade {
    use KernelHttpClientUseCase;

    public function list(): CustomerListAssertion {
        return CustomerListAssertion::ofJsonResponse(
            $this->httpClient->get('/api/v1/customers')
        );
    }

    public function create(string $name): void {
        $this->httpClient->post('/api/v1/customers', compact('name'));
    }
}
