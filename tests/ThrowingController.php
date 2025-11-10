<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

final readonly class ThrowingController {
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        throw new RuntimeException('Something went wrong');
    }
}
