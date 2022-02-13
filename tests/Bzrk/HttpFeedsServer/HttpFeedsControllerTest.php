<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;
use function PHPUnit\Framework\anything;
use function PHPUnit\Framework\assertJsonStringEqualsJsonString;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isInstanceOf;
use PHPUnit\Framework\MockObject\MockObject;
use function PHPUnit\Framework\once;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\Response;

/**
 * @internal
 */
class HttpFeedsControllerTest extends TestCase
{
    /**
     * @var MockObject&ServerRequestInterface
     */
    private ServerRequestInterface $request;

    /**
     * @var HttpFeedsFetcher&MockObject
     */
    private HttpFeedsFetcher $fetcher;

    public function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->fetcher = $this->createMock(HttpFeedsFetcher::class);
    }

    public function testUseResponseFromInput(): void
    {
        $response = new Response(200, ['cache' => 'nocache']);

        $this->request->method('getQueryParams')->willReturn([]);

        $result = (new HttpFeedsController($this->fetcher))($this->request, $response);
        assertThat($result, isInstanceOf(ResponseInterface::class));
        assertThat($result->getHeader('Cache'), equalTo(['nocache']));
    }

    public function testCreateAResponseAndUseThat(): void
    {
        $this->request->method('getQueryParams')->willReturn([]);

        $result = (new HttpFeedsController($this->fetcher))($this->request);
        assertThat($result, isInstanceOf(ResponseInterface::class));
    }

    public function testCallFetcherCorrectWithoutLastEvenetIdAndTimeout(): void
    {
        $this->request->method('getQueryParams')->willReturn([]);

        $request = new Request('', null);

        $this->fetcher->expects(once())->method('fetch')->with(equalTo($request));

        (new HttpFeedsController($this->fetcher))($this->request);
    }

    public function testCallFetcherCorrectWithoutTimeout(): void
    {
        $this->request->method('getQueryParams')->willReturn([
            HttpFeedsController::PARAM_LAST_EVENT_ID => 'aa',
        ]);

        $request = new Request('aa', null);

        $this->fetcher->expects(once())->method('fetch')->with(equalTo($request));

        (new HttpFeedsController($this->fetcher))($this->request);
    }

    public function testCallFetcherCorrect(): void
    {
        $this->request->method('getQueryParams')->willReturn([
            HttpFeedsController::PARAM_LAST_EVENT_ID => 'aa',
            HttpFeedsController::PARAM_TIMEOUT => 1,
        ]);

        $request = new Request('aa', TimeUnit::ofSeconds(1));

        $this->fetcher->expects(once())->method('fetch')->with(equalTo($request));

        (new HttpFeedsController($this->fetcher))($this->request);
    }

    public function testWriteResponseCorrect(): void
    {
        $time = new \DateTimeImmutable();

        $this->request->method('getQueryParams')->willReturn([]);

        $feedItem = new FeedItem(
            'id',
            'type',
            'source',
            $time,
            'subject',
            'method',
            (object) ['test' => 'test']
        );

        $collection = new FeedItemCollection($feedItem);

        $this->fetcher->method('fetch')->with(anything())->willReturn($collection);

        $response = (new HttpFeedsController($this->fetcher))($this->request);

        $response->getBody()->rewind();

        assertThat($response->getHeaders(), equalTo(['content-type' => ['application/cloudevents-batch+json']]));
        assertJsonStringEqualsJsonString(
            $response->getBody()->getContents(),
            (string) json_encode(
                [
                    (new CloudEventMapper())->toJsonSerializable(
                        (new FeedItemMapper())->toCloudEvent($feedItem)
                    ),
                ]
            )
        );
    }
}
