<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use PHPUnit\Framework\TestCase;
use WonderNetwork\SlimKernelTestingHarness\TestKernel;

final class SlimKernelHttpClientHttpPayloadTest extends TestCase {
    public function testPost(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $payload = ['payload' => bin2hex(random_bytes(16))];
        $actual = $sut->post('/payload', $payload)->expectJson();
        self::assertSame($payload, $actual);
    }

    public function testPatch(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $payload = ['payload' => bin2hex(random_bytes(16))];
        $actual = $sut->patch('/payload', $payload)->expectJson();
        self::assertSame($payload, $actual);
    }

    public function testPut(): void {
        $app = TestKernel::build();

        $sut = SlimKernelHttpClient::create($app);

        $payload = ['payload' => bin2hex(random_bytes(16))];
        $actual = $sut->put('/payload', $payload)->expectJson();
        self::assertSame($payload, $actual);
    }
}
