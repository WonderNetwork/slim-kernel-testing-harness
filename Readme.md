# Test harness for [SlimKernel](https://github.com/WonderNetwork/slim-kernel)

## Overview

The goal of this package is to make writing high-level tests exciting by 
building sufficient architecture around them, that would make it easy 
and expressive to build your test cases.

## Installation

```
composer require --dev wondernetwork/slim-kernel-testing-harness
```

## Core concepts

### `SlimKernelHttpClient`

```php
use WonderNetwork\SlimKernelTestingHarness\KernelHttpClient as Core;
$httpClient = Core\SlimKernelHttpClient::create($slimApp);
```

This class acts as a HttpClient directing the requests at the provided
Slim App, which is at it’s core a Request Handler. The most basic usage
is to use one of the convenience methods modeled after HTTP verbs:

 * `$httpClient->get("/url")`
 * `$httpClient->head("/url")`
 * `$httpClient->post("/url", payload: [...])`
 * `$httpClient->put("/url", payload: [...])`
 * `$httpClient->patch("/url", payload: [...])`
 * `$httpClient->delete("/url")`

Or a more advanced, by crafting the request manually (or using the provided
`RequestBuilder`) and passing it to the `request()` method.

Each request is by default expected to succeed, as determined by returning
a HTTP status code in the 1xx-3xx range. You can change the behaviour by
calling the `withResponseExpectation` method:

 * `withResponseExpectation(ResponseExpectation::Failure)` — codes greater or equal to 4xx
 * `withResponseExpectation(ResponseExpectation::Success)` — below 4xx, the default
 * `withResponseExpectation(ResponseExpectation::None)` — status code assertions disabled

_Hint_: when building test cases outside the happy path, such as making
sure your authorization mechanism works correctly, you will want to
use the `ResponseExpectation::Failure` mode.

### `HttpResponseAssertion`

Each `SlimKernelHttpClient` returns a `HttpResponseAssertion`, which helps you 
make common assertions about the response and access its contents. Example
helper methods:

  * `assertSuccess()` and `assertFailure()` (useful you called with the `ResponseExpectation::None`)
  * `expectJson()` asserts the response was a valid JSON and return its parsed data
  * `isSuccessful()` indicates if the response http code is in the 1xx-3xx range
  * `assertRedirect($to = null)` asserts the response is a redirect and optionally
    makes sure the `Location` header matches the provided value
  * `slimErrorPage()` returns an object representing the default Slim error page,
    so you can make assertions about the thrown exception type or its message

The [PSR-7](https://www.php-fig.org/psr/psr-7/) Response object is available as a
public `$response` field. For convenience, the calling `SlimKernelHttpClient` is 
also attached as `$httpClient`.

### Use Cases (`KernelHttpUseCase` trait)

To organize your test cases, and make reusable actions, it’s good to organize
your sources into use cases. These use cases are an SDK of sorts for your app.
They represent an abstraction over your application’s API. Look at the examples:

```php
class AdminUseCasesFacade {
    use KernelHttpClientUseCase;
    
    public function listOrders(): OrderListAssertion {
        return OrderListAssertion::ofJsonResponse(
            $this->httpClient->json()->get('/api/v1/orders')
        );
    }
}

class CustomerUseCasesFacade {
    use KernelHttpClientUseCase;
    
    public function makeOrder(string $productId): void {
        return $this->httpClient->post('/api/v1/orders', ['product' => $productId]);
    }
}

```

The trait is here to provide a couple of convenience methods. Since
most of your use cases will only use the `SlimKernelHttpClient` as
the entrypoint into your app, this is the only dependency for your
use cases. The trait provides the factory method `of($httpClient)`
to create an instance, the constructor to store the client in a 
protected field, and two helper methods to control the response
expectation: `expectFailure()` and `noResponseExpectation()`

```php
CustomerUseCasesFacade
  ::of($httpClient)
  ->expectFailure()
  ->makeOrder($invalidProductId)
```

### Assertions (`JsonListResponseAssertion` and others)

To build a robust test suite, most of your API endpoints will need
a custom assertion class (responsible for holding the response data
and making assertions on it). One particularly interesting instance
of this are assertions about endpoints listing multiple records.
Their handling can be made easier by using the `JsonListResponseAssertion`:

```php
class OrderListAssertion {
    use JsonListResponseAssertion;
    
    public function first(): OrderItemAssertion {
        return new OrderItemAssertion($this->items->getFirstItemData());
    }
}
```

It automatically provides the `ofJsonResponse` factory method, which
can directly take the result of a `$httpClient->request()` call. An
advanced usage of the assertions and use cases is presented in the
[examples](examples) folder.

### Middlewares

Inputs for a high-level e2e test are such complex and diverse. Different
clients craft different payloads, which include:

 * `JSON` payloads
 * `multipart/form-data` or `application/x-www-form-urlencoded` payloads
 * Requests with basic auth, bearer tokens or client certificates
 * Stateful session cookies or other similar authentication mechanisms

It’s challenging to fit all this complexity and provide keep the simple
interface such as the `SlimKernelHttpClient` has. The package solves this
problem by allowing adding a [PSR-15 middlewares](https://www.php-fig.org/psr/psr-15/)
to the client. Two basic ones are built in:

 * `$httpClient->json()` adds an `Accept` header to indicate json payloads
   are expected, as well as the `X-Requested-With` header to simulate a browser
 * `$httpClient->withBaseUrl($url)` will prefix each request url with a given value
 * use `$httpClient->withMiddleware()` to add a custom one

Custom middlewares in combination with different use cases for different
clients of your app can be very powerful. You can look at the 
(example middlewares)[examples/middlewares] to a glimpse of how they can be used.
