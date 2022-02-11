HTTP Feeds Server
===

This project is a PHP Implementation of [HTTP Feeds] (https://github.com/http-feeds/http-feeds).

## HTTP Feeds
> Asynchronous event streaming and data replication with plain HTTP APIs.
> 
> HTTP feeds is a minimal specification for polling events over HTTP:
>
> - An HTTP feed provides a HTTP GET endpoint
> - that returns a chronological sequence (!) of events
> - serialized in [CloudEvents](https://github.com/cloudevents/spec) event format
> - in batched responses using the media type `application/cloudevents-batch+json`
> - and respects the `lastEventId` query parameter to scroll through further items
> - to support [infinite polling](#polling) for real-time feed subscriptions.
>
> HTTP feeds can be used to [decouple systems](https://scs-architecture.org) asynchronously [without message brokers](https://www.innoq.com/en/articles/2021/12/http-feeds-schnittstellen-ohne-kafka-oder-rabbitmq), such as Kafka or RabbitMQ.

## Install

`composer require bzrk/http-feeds-server`

## Usage with [Slim](https://github.com/slimphp/Slim)

```php
...
class Repo implements Repository {

    public function getByIdGreaterThan(string $lastEventId, int $limit): FeedItemCollection
    {
        ....
    }
}

$fetcher = HttpFeedsFetcher::builder(new Repo())->limit(10)->build();

$app = AppFactory::create();
$app->get('/', new HttpFeedsController($fetcher));
$app->addErrorMiddleware(true, true, true);
$app->run();
```

## Usage with [ReactPHP](https://reactphp.org/http/)

```php
...
class Repo implements Repository {

    public function getByIdGreaterThan(string $lastEventId, int $limit): FeedItemCollection
    {
        ....
    }
}

$fetcher = HttpFeedsFetcher::builder(new Repo())->limit(10)->build();


$http = new React\Http\HttpServer(new HttpFeedsController($fetcher));
$socket = new React\Socket\SocketServer('0.0.0.0:8080');
$http->listen($socket);
```