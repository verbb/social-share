/**
 * Render current Social Share providers into stable public screenshot fixtures.
 *
 * craft-screenshots: sample-frontend
 */

use craft\helpers\Json;
use verbb\socialshare\SocialShare;

$providers = SocialShare::$plugin->getProviders()->getAllProviders();
$providerTiles = '';

foreach (array_slice($providers, 0, 58) as $provider) {
    $name = htmlspecialchars($provider->getName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $colour = htmlspecialchars((string)$provider->getPrimaryColor(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $providerTiles .= '<div class="provider" title="' . $name . '" style="--provider-colour:' . $colour . '">' . $provider->getIcon() . '</div>';
}

$examples = [
    ['facebook', 'icon'],
    ['mastodon', 'rounded'],
    ['dribbble', 'circle'],
    ['instagram', 'label'],
    ['twitter', 'label'],
    ['airbnb', 'pill'],
    ['discord', 'soft'],
    ['appStore', 'plain'],
    ['gitHub', 'dark'],
    ['linkedIn', 'outline'],
];
$exampleButtons = '';

foreach ($examples as [$handle, $style]) {
    $provider = SocialShare::$plugin->getProviders()->getProviderByHandle($handle);

    if (!$provider) {
        continue;
    }

    $name = htmlspecialchars($provider->getName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $colour = htmlspecialchars((string)$provider->getPrimaryColor(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $exampleButtons .= '<div class="example ' . $style . '" style="--provider-colour:' . $colour . '"><span class="icon">' . $provider->getIcon() . '</span><span class="label">' . $name . '</span></div>';
}

$baseStyles = <<<'CSS'
*{box-sizing:border-box}html,body{margin:0;background:#fff;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.provider svg,.example svg{display:block;width:100%;height:100%;fill:currentColor}
CSS;

$buttonsHtml = '<!doctype html><html><head><meta charset="utf-8"><style>' . $baseStyles . '#social-share-buttons-frame{width:825px;height:210px;padding:12px 11px;overflow:hidden;background:#fff}.providers{display:grid;grid-template-columns:repeat(15,40px);gap:10px 14px}.provider{width:40px;height:40px;padding:9px;background:var(--provider-colour);color:#fff}</style></head><body><div id="social-share-buttons-frame"><div class="providers">' . $providerTiles . '</div></div></body></html>';
$generatorHtml = '<!doctype html><html><head><meta charset="utf-8"><style>' . $baseStyles . '#social-share-generator-frame{width:550px;height:139px;padding:16px 11px;overflow:hidden;background:#fff;display:flex;flex-wrap:wrap;align-content:flex-start;gap:15px 14px}.example{height:40px;display:flex;align-items:center;color:#fff;background:var(--provider-colour);font-size:14px}.example .icon{width:40px;height:40px;padding:10px;background:rgba(0,0,0,.1)}.example .label{padding:0 14px}.example.icon .label,.example.rounded .label,.example.circle .label,.example.soft .label,.example.plain .label,.example.outline .label{display:none}.example.icon,.example.rounded,.example.circle,.example.soft,.example.plain,.example.outline{width:40px}.example.rounded{border-radius:9px;overflow:hidden}.example.circle{border-radius:999px;overflow:hidden}.example.pill{border-radius:999px;overflow:hidden}.example.soft{background:#eee;color:var(--provider-colour);border-radius:4px}.example.plain{background:transparent;color:var(--provider-colour)}.example.dark{background:#111}.example.outline{background:#fff;color:#111;border:1px solid #111}.example.outline .icon{background:transparent}</style></head><body><div id="social-share-generator-frame">' . $exampleButtons . '</div></body></html>';

file_put_contents(Craft::getAlias('@webroot/social-share-buttons-screenshot.html'), $buttonsHtml);
file_put_contents(Craft::getAlias('@webroot/social-share-generator-screenshot.html'), $generatorHtml);

echo Json::encode(['providerCount' => count($providers)], JSON_THROW_ON_ERROR);
