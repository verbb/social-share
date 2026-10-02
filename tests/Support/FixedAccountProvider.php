<?php

declare(strict_types=1);

namespace Tests\Support;

use verbb\socialshare\providers\Dribbble;

final class FixedAccountProvider extends Dribbble
{
    public static string $handle = 'fixed-account';

    public int $followerRequests = 0;

    public function getFollowersCount(string $account): ?int
    {
        $this->followerRequests++;

        return 5;
    }
}
