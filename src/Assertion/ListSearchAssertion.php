<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Assertion;

use PHPUnit\Framework\Assert;
use WonderNetwork\SlimKernel\Accessor\ArrayAccessor;
use function WonderNetwork\SlimKernel\Collection\filter;

final readonly class ListSearchAssertion {
    public static function of(array $data): self {
        return new self($data);
    }

    public function __construct(public array $data) {
    }

    public function isEmpty(): bool {
        return count($this->data) === 0;
    }

    public function filterByProp(string $prop, mixed $value): self {
        return self::of(
            filter(
                $this->data,
                static fn (array $record) => ($record[$prop] ?? null) === $value,
            ),
        );
    }

    public function column(string $column): array {
        return array_column($this->data, $column);
    }

    public function getFirstItemData(): mixed {
        Assert::assertNotCount(0, $this->data, message: "Cannot get first item of empty list.");
        return $this->data[array_key_first($this->data)];
    }

    public function getFirstItemDataAccessor(): ArrayAccessor {
        return ArrayAccessor::of($this->getFirstItemData());
    }

    public function getLastItemData(): mixed {
        Assert::assertNotCount(0, $this->data, message: "Cannot get last item of empty list.");
        return $this->data[array_key_last($this->data)];
    }

    public function getLastItemDataAccessor(): ArrayAccessor {
        return ArrayAccessor::of($this->getLastItemData());
    }
}
