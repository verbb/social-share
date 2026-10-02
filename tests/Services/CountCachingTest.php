<?php

declare(strict_types=1);

use Tests\Support\CountingProvider;
use Tests\Support\StubProviders;
use verbb\socialshare\services\Service;
use verbb\socialshare\SocialShare;

beforeEach(function(): void {
    $this->provider = new CountingProvider();
    $this->service = new Service();
    SocialShare::$plugin->set('providers', new StubProviders($this->provider));
    SocialShare::$plugin->set('service', $this->service);

    $settings = SocialShare::$plugin->getSettings();
    $settings->enableCache = true;
    $settings->cacheDuration = 3600;
    $settings->friendlyCount = false;
    $settings->minShareCount = null;
});

it('caches zero and failed count results', function(): void {
    $this->provider->shareResult = 0;

    expect($this->service->getShares('counting', 'https://example.test/zero'))->toBe('0')
        ->and($this->service->getShares('counting', 'https://example.test/zero'))->toBe('0')
        ->and($this->provider->shareRequests)->toBe(1);

    $this->provider->shareResult = null;

    expect($this->service->getShares('counting', 'https://example.test/failure'))->toBeNull()
        ->and($this->service->getShares('counting', 'https://example.test/failure'))->toBeNull()
        ->and($this->provider->shareRequests)->toBe(2);
});

it('applies the minimum share count to freshly fetched and cached values', function(): void {
    SocialShare::$plugin->getSettings()->minShareCount = 10;
    $this->provider->shareResult = 5;

    expect($this->service->getShares('counting', 'https://example.test/low'))->toBeNull()
        ->and($this->service->getShares('counting', 'https://example.test/low'))->toBeNull()
        ->and($this->provider->shareRequests)->toBe(1);

    $this->provider->shareResult = 50;

    expect($this->service->getShares('counting', 'https://example.test/high'))->toBe('50')
        ->and($this->service->getShares('counting', 'https://example.test/high'))->toBe('50')
        ->and($this->provider->shareRequests)->toBe(2);
});
