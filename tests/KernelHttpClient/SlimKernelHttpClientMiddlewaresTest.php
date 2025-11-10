<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WonderNetwork\SlimKernelTestingHarness\TestKernel;

final class SlimKernelHttpClientMiddlewaresTest extends TestCase {
    public function testMiddlewareCanBeAttached(): void {
        $value = bin2hex(random_bytes(16));
        $middleware = new class ($value) implements MiddlewareInterface {
            public function __construct(private string $value) {
            }

            public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
                return $handler->handle(
                    RequestBuilder::ofRequest($request)
                        ->withServerParam('SSL_CLIENT_CERT', $this->value)
                        ->build(),
                );
            }
        };

        $app = TestKernel::build();
        $sut = SlimKernelHttpClient
            ::create($app)
            ->withMiddleware($middleware);

        $actual = $sut->get('/server-params')->expectJson();

        self::assertArrayHasKey('SSL_CLIENT_CERT', $actual);
        self::assertSame($value, $actual['SSL_CLIENT_CERT']);
    }
}
