<?php
namespace verbb\socialshare\controllers;

use verbb\socialshare\SocialShare;
use verbb\socialshare\base\OAuthProvider;

use Craft;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class ProvidersController extends Controller
{
    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->requireCpRequest();
        $this->requireAdmin();

        return true;
    }

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

        $handle = $this->request->getRequiredBodyParam('handle');
        $settings = $this->request->getBodyParam('settings', []);

        if (!is_string($handle) || $handle === '') {
            throw new BadRequestHttpException('Provider handle must be a non-empty string.');
        }

        if (!is_array($settings)) {
            throw new BadRequestHttpException('Provider settings must be an array.');
        }

        $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

        if (!$provider) {
            throw new HttpException(404);
        }

        // A provider's settings contract is also the persistence allowlist for custom providers.
        $settings = array_intersect_key($settings, array_flip($provider->settingsAttributes()));

        if (!SocialShare::$plugin->getProviders()->saveProvider($provider, $settings)) {
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
