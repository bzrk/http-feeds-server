<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\Response;

class HttpFeedsController
{
    public const PARAM_LAST_EVENT_ID = 'lastEventId';
    public const PARAM_TIMEOUT = 'timeout';

    private FeedItemMapper $mapper;

    public function __construct(private HttpFeedsFetcher $fetcher)
    {
        $this->mapper = new FeedItemMapper();
    }

    public function __invoke(ServerRequestInterface $request, ?ResponseInterface $response = null): ResponseInterface
    {
        $params = $request->getQueryParams();

        $fetchRequest = new Request(
            $params[self::PARAM_LAST_EVENT_ID] ?? '',
            isset($params[self::PARAM_TIMEOUT]) ? TimeUnit::ofSeconds((int) $params[self::PARAM_TIMEOUT]) : null,
        );

        $data = $this->fetcher->fetch($fetchRequest)->stream()
            ->map(fn (FeedItem $feedItem) => $this->mapper->toJsonSerializable($feedItem))
            ->toList()
        ;

        $response = $response ?: new Response();

        $response->getBody()->write((string) json_encode($data));

        return $response->withHeader('content-type', 'application/cloudevents-batch+json');
    }
}
