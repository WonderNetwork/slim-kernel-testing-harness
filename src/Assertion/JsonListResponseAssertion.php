<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Assertion;

use PHPUnit\Framework\Assert;
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\HttpResponseAssertion;

/**
 * Represents a HttpResponse of a list endpoint
 */
trait JsonListResponseAssertion {
    public static function ofJsonResponse(HttpResponseAssertion $response, string $path = null): static {
        $data = $response->expectJson();
        $items = $data;
        if ($path) {
            Assert::assertArrayHasKey($path, $items, "No items found under $path key.");
            $items = $items[$path];
            Assert::assertIsArray($items, "Value at $path key is not an array.");
        }

        return new self(
            response: $response,
            items: ListSearchAssertion::of($items),
        );
    }

    public function __construct(
        protected readonly HttpResponseAssertion $response,
        protected readonly ListSearchAssertion $items,
    ) {
    }

    public function filteredBy(string $prop, mixed $value): self {
        return new self(
            response: $this->response,
            items: $this->items->filterByProp($prop, $value),
        );
    }

    public function expectCount(int $count, string $message = ""): self {
        Assert::assertCount($count, $this->items->data, $message);

        return $this;
    }
}
