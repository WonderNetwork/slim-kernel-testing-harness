<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use WonderNetwork\SlimKernelTestingHarness\Assertion\JsonListResponseAssertion;

final readonly class CustomerUserListAssertion {
    use JsonListResponseAssertion;

    public function first(): CustomerUserAssertion {
        $data = $this->items->getFirstItemDataAccessor();
        return new CustomerUserAssertion(
            id: $data->string('id'),
            name: $data->string('name'),
            httpClient: $this->response->httpClient,
        );
    }

    public function named(string $name): CustomerUserAssertion {
        return $this->filteredBy('name', $name)->first();
    }
}
