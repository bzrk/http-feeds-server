<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;

class Request
{
    public function __construct(private string $lastEventId, private ?TimeUnit $timeOut = null)
    {
    }

    public function lastEventId(): string
    {
        return $this->lastEventId;
    }

    public function timeOut(): ?TimeUnit
    {
        return $this->timeOut;
    }
}
