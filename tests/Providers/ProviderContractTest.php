<?php

declare(strict_types=1);

use Tests\Support\HttpFake;
use verbb\socialshare\providers\Envato;
use verbb\socialshare\providers\Mailchimp;
use verbb\socialshare\providers\Reddit;
use verbb\socialshare\providers\Spotify;
use verbb\socialshare\providers\Yummly;
use verbb\socialshare\services\Providers;

beforeEach(function(): void {
    HttpFake::reset();
});

it('instantiates every registered provider with a unique handle', function(): void {
    $providers = (new Providers())->getAllProviders();
    $handles = array_map(static fn($provider): string => $provider->getHandle(), $providers);

    expect($providers)->toHaveCount(count(array_unique($handles)))
        ->and(count($providers))->toBeGreaterThan(100);
});

it('does not advertise removed upstream count capabilities', function(): void {
    expect(Reddit::supportsSharesCount())->toBeFalse()
        ->and(Spotify::supportsFollowersCount())->toBeFalse()
        ->and(Yummly::supportsSharesCount())->toBeFalse();
});

it('fails closed before outbound requests when credential providers are unconfigured', function(): void {
    $mailchimp = new Mailchimp();
    $envato = new Envato();

    expect($mailchimp->isConfigured())->toBeFalse()
        ->and($envato->isConfigured())->toBeFalse()
        ->and($mailchimp->getFollowersCount('list'))->toBeNull()
        ->and($envato->getFollowersCount('account'))->toBeNull()
        ->and(HttpFake::$requests)->toBe([]);
});
