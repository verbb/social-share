<?php

declare(strict_types=1);

use verbb\socialshare\providers\Email;
use verbb\socialshare\providers\Facebook;
use verbb\socialshare\providers\PrintProvider;
use verbb\socialshare\providers\Sms;
use verbb\socialshare\SocialShare;
use verbb\socialshare\web\assets\frontend\ShareButtonAsset;

it('encodes email and SMS spaces without double encoding', function(): void {
    $email = (new Email())->getShareUrl('https://example.test/a path', 'A subject');
    $sms = (new Sms())->getShareUrl('https://example.test/a path', 'A message');

    expect($email)->toContain('%20')->not->toContain('+')->not->toContain('%2520')
        ->and($sms)->toContain('%20')->not->toContain('+')->not->toContain('%2520');
});

it('renders a real CSP-compatible share link and registers its first-party behavior', function(): void {
    $view = Craft::$app->getView();
    unset($view->assetBundles[ShareButtonAsset::class]);
    $button = (new Facebook())->getShareButton([
        'url' => 'https://example.test/article',
        'text' => 'Article',
    ]);
    $attributes = $button->getButtonAttributes();

    expect($attributes['href'])->toStartWith('https://www.facebook.com/sharer/sharer.php')
        ->and($attributes['target'])->toBe('_blank')
        ->and($attributes['rel'])->toContain('noopener')
        ->and($attributes['data-social-share-popup'])->toBeTrue()
        ->and($attributes)->not->toHaveKey('onclick')
        ->and($attributes['href'])->not->toStartWith('javascript:')
        ->and($view->assetBundles)->toHaveKey(ShareButtonAsset::class);
});

it('keeps ordinary link behavior when modal sharing is disabled', function(): void {
    SocialShare::$plugin->getSettings()->useModalForShare = false;
    $attributes = (new Facebook())->getShareButton([
        'url' => 'https://example.test/article',
    ])->getButtonAttributes();

    expect($attributes['href'])->toStartWith('https://www.facebook.com/sharer/sharer.php')
        ->and($attributes)->not->toHaveKey('data-social-share-popup')
        ->and($attributes)->not->toHaveKey('data-url');
});

it('renders printing as delegated CSP-compatible behavior', function(): void {
    $attributes = (new PrintProvider())->getShareButton([
        'url' => 'https://example.test/article',
    ])->getButtonAttributes();

    expect($attributes['href'])->toBe('#')
        ->and($attributes['data-social-share-print'])->toBeTrue()
        ->and($attributes)->not->toHaveKey('onclick')
        ->and($attributes)->not->toHaveKey('target')
        ->and($attributes)->not->toHaveKey('rel');
});
