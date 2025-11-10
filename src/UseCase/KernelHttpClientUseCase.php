<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\UseCase;

use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\ResponseExpectation;
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\SlimKernelHttpClient;

trait KernelHttpClientUseCase {
    public static function of(SlimKernelHttpClient $httpClient): self {
        return new self($httpClient);
    }

    protected function __construct(
        protected readonly SlimKernelHttpClient $httpClient,
    ) {
    }

    public function noResponseExpectation(): self {
        return new self(
            $this->httpClient->withResponseExpectation(
                ResponseExpectation::None,
            ),
        );
    }

    public function expectFailure(): self {
        return new self(
            $this->httpClient->withResponseExpectation(
                ResponseExpectation::Failure,
            ),
        );
    }
}
