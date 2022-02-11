<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

interface Repository
{
    public function getByIdGreaterThan(string $lastEventId, int $limit): FeedItemCollection;
}
