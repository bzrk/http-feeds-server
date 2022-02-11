<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;
use Psr\Log\LoggerInterface;

class HttpFeedsFetcher
{
    public function __construct(
        private Repository $repository,
        private TimeUnit $pollIntervall,
        private int $limit,
        private ?LoggerInterface $logger = null
    ) {
    }

    public static function builder(Repository $repository): HttpFeedsFetcherBuilder
    {
        return new HttpFeedsFetcherBuilder($repository);
    }

    public function fetch(Request $request): FeedItemCollection
    {
        $this->logger?->info("Fetch, [lastId={$request->lastEventId()}, timeOut = {$request->timeOut()?->seconds()}]");

        if (null !== $request->timeOut()) {
            return $this->fetchWithPolling($request->lastEventId(), $request->timeOut());
        }

        return $this->fetchWithOutPolling($request->lastEventId());
    }

    private function fetchWithOutPolling(string $lastEventId): FeedItemCollection
    {
        return $this->repository->getByIdGreaterThan($lastEventId, $this->limit);
    }

    private function fetchWithPolling(string $lastEventId, TimeUnit $timeOut): FeedItemCollection
    {
        $timeout = time() + $timeOut->seconds();

        while (true) {
            $items = $this->repository->getByIdGreaterThan($lastEventId, $this->limit);
            if ($items->count() > 0) {
                return $items;
            }

            if (time() > $timeout) {
                break;
            }

            $this->pollIntervall->sleep();
        }

        return new FeedItemCollection();
    }
}
