<?php
namespace verbb\socialshare\controllers;

use verbb\socialshare\SocialShare;

use Craft;
use craft\elements\User;
use craft\web\Controller;

use yii\web\Response;

use verbb\auth\Auth;
use verbb\auth\helpers\Session;

use Throwable;

class AuthController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = ['callback'];


    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        // Don't require CSRF validation for callback requests
        if ($action->id === 'callback') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionConnect(): ?Response
    {
        $this->requireAdmin();
        $this->requirePostRequest();

        $providerHandle = $this->request->getRequiredParam('provider');
        $provider = null;

        try {
            if (!($provider = SocialShare::$plugin->getProviders()->getProviderByHandle($providerHandle))) {
                return $this->asFailure(Craft::t('social-share', 'Unable to find provider “{provider}”.', ['provider' => $providerHandle]));
            }

            $context = [
                'providerHandle' => $providerHandle,
            ];

            if ($this->request->getIsCpRequest()) {
                if ($redirect = $this->request->getValidatedBodyParam('redirect')) {
                    $context['redirect'] = $this->getView()->renderObjectTemplate($redirect, $provider);
                }
            }

            return Auth::getInstance()->getOAuth()->connect('social-share', $provider, $provider->handle, $context);
        } catch (Throwable $e) {
            $providerHandle = $provider?->handle ?? 'unknown';

            SocialShare::error(sprintf(
                'Unable to authorize provider connection for “%s” (%s).',
                $providerHandle,
                $e::class,
            ));

            return $this->asFailure(Craft::t('social-share', 'Unable to authorize connect “{provider}”.', ['provider' => $providerHandle]));
        }
    }

    public function actionCallback(): ?Response
    {
        $oauth = Auth::getInstance()->getOAuth();

        if ($response = $oauth->prepareCallback('social-share')) {
            return $response;
        }

        $oauth->claimAuthorizedCallback('social-share', fn(User $user): bool => $user->admin);
        
        // Get both the origin (failure) and redirect (success) URLs
        $origin = Session::get('origin');
        $redirect = Session::get('redirect');

        // Get the provider we're current authorizing
        if (!($providerHandle = Session::get('providerHandle'))) {
            Session::setError('social-share', Craft::t('social-share', 'Unable to find provider.'), true);

            return $this->redirect($origin);
        }

        if (!($provider = SocialShare::$plugin->getProviders()->getProviderByHandle($providerHandle))) {
            Session::setError('social-share', Craft::t('social-share', 'Unable to find provider “{provider}”.', ['provider' => $providerHandle]), true);

            return $this->redirect($origin);
        }

        try {
            // Fetch the access token from the provider and create a Token for us to use
            $token = $oauth->callback('social-share', $provider, $provider->handle);

            if (!$token) {
                Session::setError('social-share', Craft::t('social-share', 'Unable to fetch token.'), true);

                return $this->redirect($origin);
            }

            // Save the token to the Auth plugin, with a reference to this provider
            $token->reference = $provider->handle;
            Auth::getInstance()->getTokens()->upsertToken($token);
        } catch (Throwable $e) {
            $error = Craft::t('social-share', 'Unable to process callback for “{provider}”.', ['provider' => $provider->name]);

            SocialShare::error(sprintf(
                'Unable to process callback for provider “%s” (%s).',
                $provider->handle,
                $e::class,
            ));

            // Show a generic error in the CP
            Craft::$app->getSession()->setFlash('social-share:callback-error', $error);

            return $this->redirect($origin);
        }

        Session::setNotice('social-share', Craft::t('social-share', '{provider} connected.', ['provider' => $provider->name]), true);

        return $this->redirect($redirect);
    }

    public function actionDisconnect(): ?Response
    {
        $this->requireAdmin();
        $this->requirePostRequest();

        $providerHandle = $this->request->getRequiredParam('provider');

        if (!($provider = SocialShare::$plugin->getProviders()->getProviderByHandle($providerHandle))) {
            return $this->asFailure(Craft::t('social-share', 'Unable to find provider “{provider}”.', ['provider' => $providerHandle]));
        }

        // Delete all tokens for this provider
        Auth::getInstance()->getTokens()->deleteTokenByOwnerReference('social-share', $provider->handle);

        return $this->asModelSuccess($provider, Craft::t('social-share', '{provider} disconnected.', ['provider' => $provider->name]), 'provider');
    }

}
