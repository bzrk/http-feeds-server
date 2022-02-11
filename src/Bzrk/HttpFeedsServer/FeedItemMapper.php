<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

class FeedItemMapper
{
    public function toJsonSerializable(FeedItem $feedItem): \JsonSerializable
    {
        return new class($feedItem) implements \JsonSerializable {
            public function __construct(private FeedItem $feedItem)
            {
            }

            /**
             * @return array<string>
             */
            public function jsonSerialize(): array
            {
                return [
                    'id' => $this->feedItem->id(),
                    'data' => $this->feedItem->data(),
                    'time' => $this->feedItem->time()->format('Y-m-d\TH:i:sp'),
                    'method' => $this->feedItem->method(),
                    'type' => $this->feedItem->type(),
                    'source' => $this->feedItem->source(),
                    'subject' => $this->feedItem->subject(),
                ];
            }
        };
    }
}
