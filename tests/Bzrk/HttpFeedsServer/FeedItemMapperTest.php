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
class FeedItemMapperTest extends TestCase
{
    public function testMapFeedItemToJsonSerializable(): void
    {
        $time = new \DateTimeImmutable();

        $feedItem = new FeedItem(
            'id',
            'type',
            'source',
            $time,
            'subject',
            'method',
            'data'
        );

        $result = (new FeedItemMapper())->toJsonSerializable($feedItem);

        assertThat($result, isInstanceOf(\JsonSerializable::class));
        assertThat($result->jsonSerialize(), equalTo([
            'id' => 'id',
            'data' => 'data',
            'time' => $time->format('Y-m-d\TH:i:sp'),
            'method' => 'method',
            'type' => 'type',
            'source' => 'source',
            'subject' => 'subject',
        ]));
    }
}
