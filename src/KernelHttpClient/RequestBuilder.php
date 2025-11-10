<?php
declare(strict_types=1);

namespace WonderNetwork\SlimKernelTestingHarness\KernelHttpClient;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\Factory as Psr7;
use Slim\Http\Factory as Slim;

final class RequestBuilder {

    public static function of(string $method, string $uri): self {
        $factory = self::createDecoratedServerRequestFactory();
        $request = $factory->createServerRequest($method, $uri);

        return new self(
            request: $request,
            requestFactory: $factory,
        );
    }

    public static function ofRequest(ServerRequestInterface $request): self {
        $factory = self::createDecoratedServerRequestFactory();
        return new self(
            request: $request,
            requestFactory: $factory,
        );
    }

    public function __construct(
        private ServerRequestInterface $request,
        private readonly Slim\DecoratedServerRequestFactory $requestFactory,
    ) {
    }

    public function withParsedBody(array $body): self {
        $this->request = $this->request->withParsedBody($body);
        return $this;
    }

    public function withQueryParams(array $query): self {
        $this->request = $this->request->withQueryParams($query);
        return $this;
    }

    public function withQueryParam(string $name, string $value): self {
        return $this->withQueryParams([$name => $value] + $this->request->getQueryParams());
    }

    public function withIp(string $ip): self {
        return $this->withServerParam('REMOTE_ADDR', $ip);
    }

    public function withUploadedFiles(UploadedFileInterface ...$files): self {
        $this->request = $this->request->withUploadedFiles($files);
        return $this;
    }

    public function withServerParam(string $key, string $value): self {
        $parsedBody = $this->request->getParsedBody();

        $headers = $this->request->getHeaders();
        $body = $this->request->getBody();

        $this->request = $this->requestFactory
            ->createServerRequest(
                method: $this->request->getMethod(),
                uri: $this->request->getUri(),
                serverParams: [$key => $value] + $this->request->getServerParams(),
            )
            ->withBody($body);

        foreach ($headers as $headerName => $headerValue) {
            $this->request = $this->request->withHeader($headerName, $headerValue);
        }

        if (null !== $parsedBody) {
            $this->request = $this->request->withParsedBody($parsedBody);
        }

        return $this;
    }

    public function build(): ServerRequestInterface {
        return $this->request;
    }

    private static function createDecoratedServerRequestFactory(): Slim\DecoratedServerRequestFactory {
        return new Slim\DecoratedServerRequestFactory(new Psr7\ServerRequestFactory());
    }
}
