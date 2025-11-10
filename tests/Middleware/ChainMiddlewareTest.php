<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\Middleware;

use PHPUnit\Framework\TestCase;
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\SlimKernelHttpClient;
use WonderNetwork\SlimKernelTestingHarness\TestKernel;

class ChainMiddlewareTest extends TestCase {
    public function testMiddlewareOrder(): void {
        $sut = SlimKernelHttpClient
            ::create(TestKernel::build())
            ->withMiddleware(new AddToRequestPayloadParamMiddleware('alpha', 'first'))
            ->withMiddleware(new AddToRequestPayloadParamMiddleware('alpha', 'second'))
            ->withMiddleware(new AddToRequestPayloadParamMiddleware('alpha', 'third'));

        $actual = $sut->post('/payload', ['alpha' => 'initial'])->expectJson();

        self::assertSame('initial first second third', $actual['alpha']);
    }
}
