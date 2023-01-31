<?php
namespace verbb\socialshare\controllers;

use verbb\socialshare\SocialShare;
use verbb\socialshare\base\OAuthProvider;

use Craft;
use craft\web\Controller;

use yii\web\HttpException;
use yii\web\Response;

class ProvidersController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $providers = SocialShare::$plugin->getProviders()->getAllProviders();

        return $this->renderTemplate('social-share/settings/providers', [
            'providers' => $providers,
        ]);
    }

    public function actionEdit(string $handle): Response
    {
        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider) {
            throw new HttpException(404);
        }

        return $this->renderTemplate('social-share/settings/providers/_edit', [
            'provider' => $provider,
            'isOAuth' => $provider instanceof OAuthProvider,
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $handle = $request->getParam('handle');
        $settings = $request->getParam('settings');

        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider) {
            throw new HttpException(404);
        }

        $provider->setAttributes($settings, false);

        if (!SocialShare::$plugin->getProviders()->saveProvider($provider)) {
            Craft::$app->getSession()->setError(Craft::t('social-share', 'Couldn’t save provider.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'provider' => $provider,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('social-share', 'Provider saved.'));

        return $this->redirectToPostedUrl();
    }

}