<?php
namespace verbb\socialshare\controllers;

use verbb\socialshare\SocialShare;

use craft\web\Controller;

use yii\web\Response;

class PluginController extends Controller
{
    public function actionSettings(): Response
    {
        $settings = SocialShare::$plugin->getSettings();

        return $this->renderTemplate('social-share/settings/general', [
            'settings' => $settings,
        ]);
    }
}
