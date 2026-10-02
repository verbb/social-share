<?php

declare(strict_types=1);

use Tests\Support\AdminUser;
use Tests\Support\CpRequestContext;
use Tests\Support\NonAdminUser;
use verbb\socialshare\controllers\AuthController;
use verbb\socialshare\controllers\ProvidersController;
use verbb\socialshare\SocialShare;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;

it('restricts provider settings to administrators', function(): void {
    NonAdminUser::login();
    CpRequestContext::activate('social-share/settings/providers', 'GET');
    $controller = new ProvidersController('providers', SocialShare::$plugin);
    $controller->enableCsrfValidation = false;

    expect(fn() => $controller->beforeAction($controller->createAction('index')))
        ->toThrow(ForbiddenHttpException::class);
});

it('allows administrators to view provider settings', function(): void {
    AdminUser::login();
    CpRequestContext::activate('social-share/settings/providers', 'GET');
    $controller = new ProvidersController('providers', SocialShare::$plugin);
    $controller->enableCsrfValidation = false;

    expect($controller->beforeAction($controller->createAction('index')))->toBeTrue();
});

it('requires an administrator and POST for OAuth management', function(string $action): void {
    NonAdminUser::login();
    CpRequestContext::activate("actions/social-share/auth/{$action}", 'POST');
    $controller = new AuthController('auth', SocialShare::$plugin);
    $controller->enableCsrfValidation = false;

    expect(fn() => $controller->runAction($action))->toThrow(ForbiddenHttpException::class);

    AdminUser::login();
    CpRequestContext::activate("actions/social-share/auth/{$action}", 'GET');
    $controller = new AuthController('auth', SocialShare::$plugin);
    $controller->enableCsrfValidation = false;

    expect(fn() => $controller->runAction($action))->toThrow(MethodNotAllowedHttpException::class);
})->with(['connect', 'disconnect']);
