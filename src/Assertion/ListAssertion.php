<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Assertion;

trait ListAssertion {
    public static function of(array $data): static {
        return new static(ListSearchAssertion::of($data));
    }

    public function __construct(protected readonly ListSearchAssertion $items) {
    }
}
