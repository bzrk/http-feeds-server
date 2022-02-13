<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\identicalTo;
use function PHPUnit\Framework\isInstanceOf;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class FeedItemMapperTest extends TestCase
{
    public function testMapFeedItemToCloudEvent(): void
    {
        $time = new \DateTimeImmutable();

        $feedItem = new FeedItem(
            'id',
            'type',
            'source',
            $time,
            'subject',
            'method',
            (object) ['test' => 'test']
        );

        $result = (new FeedItemMapper())->toCloudEvent($feedItem);

        assertThat($result, isInstanceOf(CloudEvent::class));
        assertThat($result->specVersion(), equalTo('1.0'));
        assertThat($result->id(), equalTo('id'));
        assertThat($result->type(), equalTo('type'));
        assertThat($result->source(), equalTo('source'));
        assertThat($result->time(), identicalTo($time));
        assertThat($result->subject(), equalTo('subject'));
        assertThat($result->method(), equalTo('method'));
        assertThat($result->data(), equalTo(json_encode(['test' => 'test'])));
        assertThat($result->dataContentType(), equalTo('application/json'));
    }
}
