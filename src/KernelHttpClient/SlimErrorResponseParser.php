<?php

declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use PHPUnit\Framework\Assert;
use Throwable;
use WonderNetwork\SlimKernel\Accessor\ArrayAccessor;

final class SlimErrorResponseParser extends Exception {
    public static function fromResponse(HttpResponseAssertion $response): SlimErrorResponseAssertion {
        $code = $response->response->getStatusCode();
        Assert::assertGreaterThanOrEqual(
            minimum: StatusCodeInterface::STATUS_BAD_REQUEST,
            actual: $code,
            message: "Not an error response code.",
        );

        if ($response->response->getHeaderLine('Content-Type') === 'application/json') {
            $message = ArrayAccessor::of($response->expectJson())->maybeString('message');

            return $message
                ? SlimErrorResponseAssertion::ofMessage($message, $code)
                : SlimErrorResponseAssertion::ofUnknown($code);
        }

        $body = (string) $response->response->getBody();

        preg_match(
            '#<div><strong>Type:</strong>\s*(?P<type>.*?)</div>#',
            $body,
            $match,
        );
        $type = $match['type'] ?? Throwable::class;

        preg_match(
            '#<div><strong>Message:</strong>\s*(?P<message>.*?)</div>#',
            $body,
            $match,
        );

        $message = $match['message'] ?? 'Slim Application Error';

        return new SlimErrorResponseAssertion(message: $message, type: $type, code: $code);
    }
}
