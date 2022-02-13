<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isInstanceOf;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CloudEventMapperTest extends TestCase
{
    public function testMapCloudEventToJsonSerializable(): void
    {
        $time = new \DateTimeImmutable();

        $cloudEvent = new CloudEvent(
            '1.0',
            'id',
            'type',
            'source',
            $time,
            'subject',
            'method',
            'application/json',
            'data'
        );

        $result = (new CloudEventMapper())->toJsonSerializable($cloudEvent);

        assertThat($result, isInstanceOf(\JsonSerializable::class));
        assertThat($result->jsonSerialize(), equalTo([
            'specversion' => '1.0',
            'id' => 'id',
            'data' => 'data',
            'time' => $time->format('Y-m-d\TH:i:sp'),
            'method' => 'method',
            'type' => 'type',
            'source' => 'source',
            'subject' => 'subject',
            'datacontenttype' => 'application/json',
        ]));
    }
}
