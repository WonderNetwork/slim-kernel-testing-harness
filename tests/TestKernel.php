<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness;

use Slim\App;
use WonderNetwork\SlimKernel\KernelBuilder;
use WonderNetwork\SlimKernel\SlimExtension\ErrorMiddlewareConfiguration;

final readonly class TestKernel {
    public static function build(): App {
        /** @var App $app */
        $app = KernelBuilder::start(__DIR__.'/..')
            ->add([
                ErrorMiddlewareConfiguration::class => ErrorMiddlewareConfiguration::verbose(),
            ])
            ->build()
            ->get(App::class);

        $app->any('/method', MethodController::class);
        $app->any('/payload', PayloadController::class);
        $app->any('/server-params', ServerParamsController::class);
        $app->any('/error', ThrowingController::class);

        return $app;
    }
}
