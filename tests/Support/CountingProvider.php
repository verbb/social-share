<?php

declare(strict_types=1);

namespace Tests\Support;

use verbb\socialshare\base\Provider;

final class CountingProvider extends Provider
{
    public static string $handle = 'counting';

    public int $followerRequests = 0;
    public int $shareRequests = 0;
    public ?int $followerResult = 13;
    public ?int $shareResult = 7;

    public static function supportsFollowersCount(): bool
    {
        return true;
    }

    public static function supportsSharesCount(): bool
    {
        return true;
    }

    public function getFollowersCount(string $account): ?int
    {
        $this->followerRequests++;

        return $this->followerResult;
    }

    public function getSharesCount(string $url): ?int
    {
        $this->shareRequests++;

        return $this->shareResult;
    }
}
