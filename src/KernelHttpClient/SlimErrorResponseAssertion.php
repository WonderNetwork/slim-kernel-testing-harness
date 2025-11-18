<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use PHPUnit\Framework\Assert;

final class SlimErrorResponseAssertion {
    public static function ofMessage(string $message, int $code): self {
        return new self(message: $message, type: "Unknown", code: $code);
    }

    public static function ofUnknown(int $code): self {
        return new self(message: "Unknown", type: "Unknown", code: $code);
    }

    public function __construct(
        public string $message,
        public string $type,
        public int $code,
    ) {
    }

    public function assertException(string $class): self {
        Assert::assertSame($class, $this->type);

        return $this;
    }

    public function assertMessage(string $message): self {
        Assert::assertSame($message, $this->message);

        return $this;
    }

    public function assertCode(int $code): self {
        Assert::assertSame($code, $this->code);

        return $this;
    }
}
