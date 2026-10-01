<?php
namespace verbb\socialshare\models;

use verbb\socialshare\SocialShare;

use Craft;
use craft\helpers\App;
use craft\helpers\UrlHelper;

class ShareButton extends Button
{
    // Public Methods
    // =========================================================================

    public function getButtonAttributes(): array
    {
        $attributes = [
            'aria-label' => $this->getName(),
        ];

        $settings = SocialShare::$plugin->getSettings();

        if ($settings->useModalForShare) {
            $attributes['onclick'] = 'window.open(this.dataset.url, "ss_share_dialog", "width=626,height=436");';
            $attributes['href'] = 'javascript:void(0);';
            $attributes['data-url'] = $this->getProviderUrl();
        } else {
            $attributes['href'] = $this->getProviderUrl();
            $attributes['target'] = '_blank';
            $attributes['rel'] = 'nofollow noopener noreferrer';
        }

        return $this->getProvider()->getButtonAttributes($attributes);
    }

    public function getProviderUrl(): ?string
    {
        $options = $this->getRenderOptions();
        $params = $options['params'] ?? [];
        $url = $options['url'] ?? $this->_getDefaultShareUrl();
        $text = $options['text'] ?? null;

        if ($url === null) {
            return null;
        }

        return $this->getProvider()->getShareUrl($url, $text, $params);
    }


    // Private Methods
    // =========================================================================

    private function _getDefaultShareUrl(): ?string
    {
        $request = Craft::$app->getRequest();

        if ($request->getIsConsoleRequest() || !$request->getIsSiteRequest()) {
            return null;
        }

        $site = Craft::$app->getSites()->getCurrentSite();
        $baseUrl = $site->getBaseUrl();

        if (!$this->_hasTrustedSiteBaseUrl($site->getBaseUrl(false), $baseUrl)) {
            return null;
        }

        $url = null;

        if (Craft::$app->getIsInitialized()) {
            $element = Craft::$app->getUrlManager()->getMatchedElement();
            $url = $element !== false ? $element->getUrl() : null;
        }

        if (!$url) {
            $url = UrlHelper::siteUrl($request->getFullPath());
        }

        $url = $this->_removeRequestParams($url);

        return $this->_isUrlOnSiteOrigin($url, $baseUrl) ? $url : null;
    }

    private function _hasTrustedSiteBaseUrl(?string $rawBaseUrl, ?string $baseUrl): bool
    {
        if (!$rawBaseUrl || !$baseUrl) {
            return false;
        }

        $request = Craft::$app->getRequest();

        // A dynamically assigned @web alias is derived from the request host.
        if ($request->isWebAliasSetDynamically && $this->_usesWebAlias($rawBaseUrl)) {
            return false;
        }

        $parts = parse_url($baseUrl);

        return is_array($parts) &&
            isset($parts['scheme'], $parts['host']) &&
            in_array(strtolower($parts['scheme']), ['http', 'https'], true) &&
            $parts['host'] !== '' &&
            !isset($parts['user']) &&
            !isset($parts['pass']);
    }

    private function _usesWebAlias(string $baseUrl): bool
    {
        if (str_starts_with($baseUrl, '@web')) {
            return true;
        }

        preg_match_all('/\$(?:\{(\w+)\}|(\w+))/', $baseUrl, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $envValue = App::env($match[1] ?: $match[2]);

            if (is_string($envValue) && str_starts_with($envValue, '@web')) {
                return true;
            }
        }

        return false;
    }

    private function _removeRequestParams(string $url): string
    {
        $generalConfig = Craft::$app->getConfig()->getGeneral();
        $pathParam = $generalConfig->pathParam;
        $preservePathParam = $pathParam &&
            !$generalConfig->omitScriptNameInUrls &&
            !$generalConfig->usePathInfo;
        $params = array_unique([
            $generalConfig->tokenParam,
            $generalConfig->siteToken,
            'x-craft-preview',
            'x-craft-live-preview',
        ]);

        foreach ($params as $param) {
            if ($param && (!$preservePathParam || $param !== $pathParam)) {
                $url = UrlHelper::removeParam($url, $param);
            }
        }

        return $url;
    }

    private function _isUrlOnSiteOrigin(string $url, string $baseUrl): bool
    {
        $urlParts = parse_url($url);
        $baseParts = parse_url($baseUrl);

        if (!is_array($urlParts) || !is_array($baseParts) ||
            !isset($urlParts['scheme'], $urlParts['host'], $baseParts['scheme'], $baseParts['host']) ||
            isset($urlParts['user']) || isset($urlParts['pass'])) {
            return false;
        }

        $urlScheme = strtolower($urlParts['scheme']);
        $baseScheme = strtolower($baseParts['scheme']);

        return in_array($urlScheme, ['http', 'https'], true) &&
            $urlScheme === $baseScheme &&
            strcasecmp($urlParts['host'], $baseParts['host']) === 0 &&
            $this->_urlPort($urlParts, $urlScheme) === $this->_urlPort($baseParts, $baseScheme);
    }

    private function _urlPort(array $parts, string $scheme): int
    {
        return $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
    }
}
