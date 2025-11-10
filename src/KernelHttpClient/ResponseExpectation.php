<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

enum ResponseExpectation {
    case Failure;
    case Success;
    case None;
}
