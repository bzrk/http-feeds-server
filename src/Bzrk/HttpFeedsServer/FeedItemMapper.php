<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

class FeedItemMapper
{
    public function toCloudEvent(FeedItem $feedItem): CloudEvent
    {
        return new CloudEvent(
            '1.0',
            $feedItem->id(),
            $feedItem->type(),
            $feedItem->source(),
            $feedItem->time(),
            $feedItem->subject(),
            $feedItem->method(),
            'application/json',
            (string) json_encode($feedItem->data()),
        );
    }
}
