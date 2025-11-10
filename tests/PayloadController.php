<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class PayloadController {
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
        $response->getBody()->write(json_encode($request->getParsedBody()));
        return $response;
    }
}
