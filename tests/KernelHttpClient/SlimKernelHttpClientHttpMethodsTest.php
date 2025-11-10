<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use PHPUnit\Framework\TestCase;
use WonderNetwork\SlimKernelTestingHarness\TestKernel;

final class SlimKernelHttpClientHttpMethodsTest extends TestCase {
    public function testGet(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $actual = (string) $sut->get('/method')->response->getBody();
        self::assertSame('GET', $actual);
    }

    public function testHead(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $actual = (string) $sut->head('/method')->response->getBody();
        // because HEAD does not return the response, doh!
        self::assertEmpty($actual);
    }

    public function testPost(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $actual = (string) $sut->post('/method')->response->getBody();
        self::assertSame('POST', $actual);
    }

    public function testPatch(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $actual = (string) $sut->patch('/method')->response->getBody();
        self::assertSame('PATCH', $actual);
    }

    public function testPut(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $actual = (string) $sut->put('/method')->response->getBody();
        self::assertSame('PUT', $actual);
    }

    public function testDelete(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $actual = (string) $sut->delete('/method')->response->getBody();
        self::assertSame('DELETE', $actual);
    }
}
