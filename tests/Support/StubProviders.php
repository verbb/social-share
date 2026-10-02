<?php

declare(strict_types=1);

namespace Tests\Support;

use verbb\socialshare\base\ProviderInterface;
use verbb\socialshare\services\Providers;

final class StubProviders extends Providers
{
    public function __construct(public ProviderInterface $provider, array $config = [])
    {
        parent::__construct($config);
    }

    public function getAllProviders(): array
    {
        return [$this->provider];
    }

    public function getProviderByHandle(string $handle): ?ProviderInterface
    {
        return $handle === $this->provider->getHandle() ? $this->provider : null;
    }
}
