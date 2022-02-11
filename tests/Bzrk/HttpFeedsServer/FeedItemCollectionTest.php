<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\PHPStream\Collection;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\identicalTo;
use function PHPUnit\Framework\isInstanceOf;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class FeedItemCollectionTest extends TestCase
{
    public function testIsInstanceOfCollection(): void
    {
        assertThat(new FeedItemCollection(), isInstanceOf(Collection::class));
    }

    public function testCurrentWillReturnTheSameFeedItem(): void
    {
        $feedItem = $this->createMock(FeedItem::class);
        $collection = new FeedItemCollection($feedItem);

        assertThat($collection->count(), equalTo(1));
        assertThat($collection->current(), identicalTo($feedItem));
    }
}
