<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;
use Psr\Log\LoggerInterface;

class HttpFeedsFetcherBuilder
{
    private TimeUnit $pollingTime;
    private int $limit;
    private ?LoggerInterface $logger = null;

    public function __construct(private Repository $repository)
    {
        $this->pollingTime = TimeUnit::ofSeconds(1);
        $this->limit = 200;
    }

    public function pollingTime(TimeUnit $pollingTime): self
    {
        $this->pollingTime = $pollingTime;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function logger(?LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function build(): HttpFeedsFetcher
    {
        return new HttpFeedsFetcher(
            $this->repository,
            $this->pollingTime,
            $this->limit,
            $this->logger
        );
    }
}
