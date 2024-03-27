<?php
namespace verbb\socialshare\controllers;

use verbb\socialshare\SocialShare;

use Craft;
use craft\web\Controller;

use yii\web\Response;

use verbb\auth\Auth;
use verbb\auth\helpers\Session;

use Throwable;

class AuthController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = ['connect', 'callback'];


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
        $providerHandle = $this->request->getRequiredParam('provider');

        try {
            if (!($provider = SocialShare::$plugin->getProviders()->getProviderByHandle($providerHandle))) {
                return $this->asFailure(Craft::t('social-share', 'Unable to find provider “{provider}”.', ['provider' => $providerHandle]));
            }

            // Keep track of which provider instance is for, so we can fetch it in the callback
            Session::set('providerHandle', $providerHandle);

            return Auth::getInstance()->getOAuth()->connect('social-share', $provider);
        } catch (Throwable $e) {
            SocialShare::error('Unable to authorize connect “{provider}”: “{message}” {file}:{line}', [
                'provider' => $providerHandle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->asFailure(Craft::t('social-share', 'Unable to authorize connect “{provider}”.', ['provider' => $providerHandle]));
        }
    }

    public function actionCallback(): ?Response
    {
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
            $token = Auth::getInstance()->getOAuth()->callback('social-share', $provider);

            if (!$token) {
                Session::setError('social-share', Craft::t('social-share', 'Unable to fetch token.'), true);

                return $this->redirect($origin);
            }

            // Save the token to the Auth plugin, with a reference to this provider
            $token->reference = $provider->handle;
            Auth::getInstance()->getTokens()->upsertToken($token);
        } catch (Throwable $e) {
            $error = Craft::t('social-share', 'Unable to process callback for “{provider}”: “{message}” {file}:{line}', [
                'provider' => $providerHandle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            SocialShare::error($error);

            // Show the error detail in the CP
            Craft::$app->getSession()->setFlash('social-share:callback-error', $error);

            return $this->redirect($origin);
        }

        Session::setNotice('social-share', Craft::t('social-share', '{provider} connected.', ['provider' => $provider->name]), true);

        return $this->redirect($redirect);
    }

    public function actionDisconnect(): ?Response
    {
        $providerHandle = $this->request->getRequiredParam('provider');

        if (!($provider = SocialShare::$plugin->getProviders()->getProviderByHandle($providerHandle))) {
            return $this->asFailure(Craft::t('social-share', 'Unable to find provider “{provider}”.', ['provider' => $providerHandle]));
        }

        // Delete all tokens for this provider
        Auth::getInstance()->getTokens()->deleteTokenByOwnerReference('social-share', $provider->handle);

        return $this->asModelSuccess($provider, Craft::t('social-share', '{provider} disconnected.', ['provider' => $provider->name]), 'provider');
    }

}
