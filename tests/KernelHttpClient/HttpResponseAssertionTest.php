<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Slim\Exception\HttpNotFoundException;
use WonderNetwork\SlimKernelTestingHarness\TestKernel;

class HttpResponseAssertionTest extends TestCase {
    public function testNotFound(): void {
        $sut = SlimKernelHttpClient::create(TestKernel::build());
        $sut
            ->withResponseExpectation(ResponseExpectation::Failure)
            ->get('/404')
            ->slimErrorPage()
            ->assertCode(404)
            ->assertException(HttpNotFoundException::class)
            ->assertMessage('Not found.');
    }

    public function testSuccess(): void {
        $sut = SlimKernelHttpClient::create(TestKernel::build());
        $sut->get('/method');
        $this->expectNotToPerformAssertions();
    }

    public function testSuccessExpectationFailed(): void {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that 500 is less than 400.');
        $sut = SlimKernelHttpClient::create(TestKernel::build());
        $sut
            ->withResponseExpectation(ResponseExpectation::Success)
            ->get('/error');
    }

    public function testFailureExpectationFailed(): void {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that 200 is equal to 400 or is greater than 400.');
        $sut = SlimKernelHttpClient::create(TestKernel::build());
        $sut
            ->withResponseExpectation(ResponseExpectation::Failure)
            ->get('/method');
    }

    public function testFailure(): void {
        $sut = SlimKernelHttpClient::create(TestKernel::build());
        $sut
            ->withResponseExpectation(ResponseExpectation::Failure)
            ->get('/error')
            ->slimErrorPage()
            ->assertException(RuntimeException::class)
            ->assertCode(500)
            ->assertMessage('Something went wrong');
    }
}
