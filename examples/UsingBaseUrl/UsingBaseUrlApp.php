<?php

declare(strict_types=1);

namespace Acme\Example\UsingBaseUrl;

use PDO;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Slim\App;
use Slim\Exception\HttpBadRequestException;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Response;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use WonderNetwork\SlimKernel\KernelBuilder;
use WonderNetwork\SlimKernel\SlimExtension\ErrorMiddlewareConfiguration;
use function DI\autowire;
use function DI\get;

final readonly class UsingBaseUrlApp {
    public static function create(string $filename): App {
        /** @var App $app */
        $app = KernelBuilder::start(__DIR__.'/../..')
            ->add([
                ErrorMiddlewareConfiguration::class => ErrorMiddlewareConfiguration::verbose(),
                StreamFactory::class => autowire(StreamFactory::class),
                StreamFactoryInterface::class => get(StreamFactory::class),
                ResponseFactory::class => autowire(ResponseFactory::class),
                DecoratedResponseFactory::class => autowire(DecoratedResponseFactory::class)->constructor(
                    get(ResponseFactory::class),
                ),
                ResponseFactoryInterface::class => get(DecoratedResponseFactory::class),
            ])
            ->build()
            ->get(App::class);

        $db = new PDO(sprintf('sqlite:%s', $filename));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('CREATE TABLE customers (id CHAR(32) PRIMARY KEY, name VARCHAR(255))');
        $db->exec('CREATE TABLE users (id CHAR(32) PRIMARY KEY, customerId CHAR(32), name VARCHAR(255))');

        $app->group('/api/v1', function (RouteCollectorProxy $app) use ($db) {
            $app->group('/customers', function (RouteCollectorProxy $app) use ($db){
                $app->get('', function (Response $response) use ($db){
                    return $response->withJson(
                        $db->query('SELECT * FROM customers')->fetchAll(PDO::FETCH_ASSOC),
                    );
                });

                $app->post('', function (ServerRequestInterface $request, Response $response) use ($db) {
                    $name = $request->getParsedBody()['name'];
                    if (!$name) {
                        throw new HttpBadRequestException($request);
                    }

                    $db
                        ->prepare('INSERT INTO customers (id, name) VALUES (:id, :name)')
                        ->execute([
                            ":id" => bin2hex(random_bytes(16)),
                            ":name" => $name,
                        ]);
                    return $response->withStatus(201);
                });

                $app->group('/{customerId}/users', function (RouteCollectorProxy $app) use ($db) {
                    $app->get('', function (ServerRequestInterface $request, Response $response) use ($db) {
                        $route = RouteContext::fromRequest($request)->getRoute()?->getArguments() ?? [];
                        $customerId = $route['customerId'];
                        $sql = $db->prepare('SELECT * FROM users WHERE customerId = :id');
                        $sql->execute([':id' => $customerId]);
                        return $response->withJson($sql->fetchAll(PDO::FETCH_ASSOC));
                    });

                    $app->post('', function (ServerRequestInterface $request, Response $response) use ($db) {
                        $route = RouteContext::fromRequest($request)->getRoute()?->getArguments() ?? [];
                        $customerId = $route['customerId'];
                        $name = $request->getParsedBody()['name'];
                        if (!$name) {
                            throw new HttpBadRequestException($request);
                        }

                        $db
                            ->prepare('INSERT INTO users (id, customerId, name) VALUES (:id, :customerId, :name)')
                            ->execute([
                                ":id" => bin2hex(random_bytes(16)),
                                ":customerId" => $customerId,
                                ":name" => $name,
                            ]);

                        return $response->withStatus(201);
                    });

                    $app->delete('/{userId}', function (ServerRequestInterface $request, Response $response) use ($db) {
                        $route = RouteContext::fromRequest($request)->getRoute()?->getArguments() ?? [];
                        $customerId = $route['customerId'];
                        $userId = $route['userId'];

                        $db
                            ->prepare('DELETE FROM users where id = :id and customerId = :customerId')
                            ->execute([
                                ":id" => $userId,
                                ":customerId" => $customerId,
                            ]);

                        return $response->withStatus(201);
                    });
                });
            });

        });

        return $app;
    }
}
