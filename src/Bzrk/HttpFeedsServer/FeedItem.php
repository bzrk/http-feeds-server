<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

class FeedItem
{
    public function __construct(
        private string $id,
        private string $type,
        private string $source,
        private \DateTimeImmutable $time,
        private string $subject,
        private string $method,
        private string $data
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function time(): \DateTimeImmutable
    {
        return $this->time;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function data(): string
    {
        return $this->data;
    }
}
