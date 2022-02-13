<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

class CloudEventMapper
{
    public function toJsonSerializable(CloudEvent $cloudEvent): \JsonSerializable
    {
        return new class($cloudEvent) implements \JsonSerializable {
            public function __construct(private CloudEvent $cloudEvent)
            {
            }

            /**
             * @return array<string>
             */
            public function jsonSerialize(): array
            {
                return [
                    'specversion' => $this->cloudEvent->specVersion(),
                    'id' => $this->cloudEvent->id(),
                    'data' => $this->cloudEvent->data(),
                    'time' => $this->cloudEvent->time()->format('Y-m-d\TH:i:sp'),
                    'method' => $this->cloudEvent->method(),
                    'type' => $this->cloudEvent->type(),
                    'source' => $this->cloudEvent->source(),
                    'subject' => $this->cloudEvent->subject(),
                    'datacontenttype' => $this->cloudEvent->dataContentType(),
                ];
            }
        };
    }
}
