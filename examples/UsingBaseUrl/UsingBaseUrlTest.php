<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient\SlimKernelHttpClient;

final class UsingBaseUrlTest extends TestCase {
    private const string DB_FILE = __DIR__.'/data.sqlite';

    #[Test]
    public function sample_scenario(): void {
        $slim = UsingBaseUrlApp::create(self::DB_FILE);
        $httpClient = SlimKernelHttpClient::create($slim);

        $useCase = CustomersUseCasesFacade::of($httpClient);

        $useCase
            ->list()
            ->expectCount(0);

        $useCase->create('Alice');
        $useCase->create('Bob');
        $useCase->create('Carol');

        $bobsUsers = $useCase
            ->list()
            ->expectCount(3)
            ->named("Bob")
            ->users();

        $bobsUsers
            ->list()
            ->expectCount(0);

        $bobsUsers->add('Alpha');
        $bobsUsers->add('Bravo');
        $bobsUsers->add('Charlie');
        $bobsUsers->add('Delta');

        $bobsUsers->expectFailure()->add('');

        $bobsUsers
            ->list()
            ->expectCount(4);

        $bobsUsers
            ->list()
            // I have nothing against you, Charlie
            ->named('Charlie')
            ->delete();

        $bobsUsers
            ->list()
            ->expectCount(3);
    }

    protected function setUp(): void {
        if (file_exists(self::DB_FILE)) {
            unlink(self::DB_FILE);
        }
    }
}
