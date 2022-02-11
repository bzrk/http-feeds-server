<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\exactly;
use function PHPUnit\Framework\identicalTo;
use function PHPUnit\Framework\isInstanceOf;
use PHPUnit\Framework\MockObject\MockObject;
use function PHPUnit\Framework\once;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class HttpFeedsFetcherTest extends TestCase
{
    /**
     * @var MockObject&Repository
     */
    private Repository $repository;

    /**
     * @var MockObject&TimeUnit
     */
    private TimeUnit $polling;

    private HttpFeedsFetcher $fetcher;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(Repository::class);
        $this->polling = $this->createMock(TimeUnit::class);
        $this->fetcher = new HttpFeedsFetcher($this->repository, $this->polling, 10);
    }

    public function testBuilder(): void
    {
        assertThat(HttpFeedsFetcher::builder($this->repository), isInstanceOf(HttpFeedsFetcherBuilder::class));
    }

    public function testFetchDataWillCallGetByIdGreaterThanWithCorrectData(): void
    {
        $request = new Request('eventId');

        $this->repository->expects(once())
            ->method('getByIdGreaterThan')
            ->with(equalTo('eventId'), equalTo(10))
        ;

        $this->fetcher->fetch($request);
    }

    public function testFetchDataWillReturnCorrectData(): void
    {
        $request = new Request('eventId');
        $collection = $this->createMock(FeedItemCollection::class);

        $this->repository->method('getByIdGreaterThan')->willReturn($collection);

        assertThat($this->fetcher->fetch($request), identicalTo($collection));
    }

    public function testFetchLongPollingDataWillCallGetByIdGreaterThanWithCorrectData(): void
    {
        $request = new Request('eventId', TimeUnit::ofSeconds(2));
        $collection = $this->createMock(FeedItemCollection::class);
        $collection->method('count')->willReturn(1);

        $this->repository->expects(once())
            ->method('getByIdGreaterThan')
            ->with(equalTo('eventId'), equalTo(10))
            ->willReturn($collection)
        ;

        $this->fetcher->fetch($request);
    }

    public function testLongPollingFetchDataWillReturnCorrectData(): void
    {
        $request = new Request('eventId', TimeUnit::ofSeconds(2));
        $collection = $this->createMock(FeedItemCollection::class);
        $collection->method('count')->willReturn(1);

        $this->repository->method('getByIdGreaterThan')->willReturn($collection);

        assertThat($this->fetcher->fetch($request), identicalTo($collection));
    }

    public function testFetchLongPollingSleep(): void
    {
        $request = new Request('eventId', TimeUnit::ofSeconds(2));
        $collection = $this->createMock(FeedItemCollection::class);
        $collection->method('count')->willReturn(1);

        $this->repository->method('getByIdGreaterThan')->willReturn(new FeedItemCollection(), $collection);

        $this->polling->expects($this->once())->method('sleep');

        $this->fetcher->fetch($request);
    }

    public function testFetchLongPollingCallWithCorrectParametersOnSecondCall(): void
    {
        $request = new Request('eventId', TimeUnit::ofSeconds(2));
        $collection = $this->createMock(FeedItemCollection::class);
        $collection->method('count')->willReturn(1);

        $this->repository->expects(exactly(2))
            ->method('getByIdGreaterThan')
            ->with(equalTo('eventId'), equalTo(10))
            ->willReturn(new FeedItemCollection(), $collection)
        ;

        $this->fetcher->fetch($request);
    }

    public function testFetchLongPollingWillReturnTheResultFromSecondCall(): void
    {
        $request = new Request('eventId', TimeUnit::ofSeconds(2));
        $collection = $this->createMock(FeedItemCollection::class);
        $collection->method('count')->willReturn(1);

        $this->repository->expects(exactly(2))
            ->method('getByIdGreaterThan')
            ->with(equalTo('eventId'), equalTo(10))
            ->willReturn(new FeedItemCollection(), $collection)
        ;

        assertThat($this->fetcher->fetch($request), identicalTo($collection));
    }

    public function testFetchLongPollingWillReturnAEmptyCollectionAfterTimeOut(): void
    {
        $request = new Request('eventId', TimeUnit::ofSeconds(1));

        $this->repository->method('getByIdGreaterThan')->willReturn(new FeedItemCollection());

        $result = $this->fetcher->fetch($request);

        assertThat($result, isInstanceOf(FeedItemCollection::class));
        assertThat($result->count(), equalTo(0));
    }
}
