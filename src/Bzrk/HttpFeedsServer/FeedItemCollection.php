<?php

declare(strict_types=1);

namespace Bzrk\HttpFeedsServer;

use BZRK\PHPStream\Collection;

class FeedItemCollection extends Collection
{
    public function __construct(FeedItem ...$feedItems)
    {
        parent::__construct($feedItems);
    }

    public function current(): FeedItem
    {
        //@phpstan-ignore-next-line
        return $this->iterator->current();
    }
}
