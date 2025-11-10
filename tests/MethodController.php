<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class MethodController {
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->getBody()->write($request->getMethod());
        return $response;
    }
}
