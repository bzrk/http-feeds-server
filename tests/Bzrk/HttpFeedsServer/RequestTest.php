<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\TimeUnit\TimeUnit;
use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isInstanceOf;
use function PHPUnit\Framework\isNull;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class RequestTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testCreateNewObject(): void
    {
        $lastEventId = md5((string) time());
        $timeOut = random_int(1, 10);

        $request = new Request($lastEventId, TimeUnit::ofSeconds($timeOut));

        assertThat($request->lastEventId(), equalTo($lastEventId));
        assertThat($request->timeOut(), isInstanceOf(TimeUnit::class));
        assertThat($request->timeOut()?->seconds(), equalTo($timeOut));
    }

    public function testCreateNewObjectWithoutTimeout(): void
    {
        $lastEventId = md5((string) time());

        $request = new Request($lastEventId);

        assertThat($request->lastEventId(), equalTo($lastEventId));
        assertThat($request->timeOut(), isNull());
    }
}
