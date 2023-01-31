<?php
namespace verbb\socialshare\models;

use verbb\socialshare\SocialShare;

use Craft;

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
            $attributes['href'] = 'javascript:void();';
            $attributes['data-url'] = $this->getProviderUrl();
        } else {
            $attributes['href'] = $this->getProviderUrl();
            $attributes['target'] = '_blank';
            $attributes['rel'] = 'nofollow noopener noreferrer';
        }

        return $attributes;
    }

    public function getProviderUrl(): ?string
    {
        $options = $this->getRenderOptions();
        $params = $options['params'] ?? [];
        $url = $options['url'] ?? Craft::$app->getRequest()->absoluteUrl;
        $text = $options['text'] ?? null;

        return $this->getProvider()->getShareUrl($url, $text, $params);
    }
}