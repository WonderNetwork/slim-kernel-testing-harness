<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use WonderNetwork\SlimKernelTestingHarness\Assertion\JsonListResponseAssertion;

final readonly class CustomerListAssertion {
    use JsonListResponseAssertion;

    public function first(): CustomerAssertion {
        $data = $this->items->getFirstItemDataAccessor();
        return new CustomerAssertion(
            id: $data->string('id'),
            name: $data->string('name'),
            httpClient: $this->response->httpClient,
        );
    }

    public function named(string $name): CustomerAssertion {
        return $this->filteredBy('name', $name)->first();
    }
}
