<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use WonderNetwork\SlimKernelTestingHarness\UseCase\KernelHttpClientUseCase;

final readonly class CustomerUsersUseCase {
    use KernelHttpClientUseCase;

    public function list(): CustomerUserListAssertion {
        return CustomerUserListAssertion::ofJsonResponse(
            $this->httpClient->get('/users'),
        );
    }

    public function add(string $name): void {
        $this->httpClient->post('/users', compact('name'));
    }
}
