<?php
namespace verbb\socialshare\models;

use verbb\socialshare\SocialShare;
use verbb\socialshare\assetbundles\FrontEndAsset;
use verbb\socialshare\base\Provider;

use Craft;
use craft\base\Model;
use craft\helpers\ArrayHelper;
use craft\helpers\Html;
use craft\helpers\Template;

use Twig\Markup;

use JsonSerializable;

class Button extends Model implements JsonSerializable
{
    // Properties
    // =========================================================================

    private Provider $_provider;
    private array $_renderOptions = [];


    // Public Methods
    // =========================================================================

    public function jsonSerialize(): array
    {
        return array_filter([
            'name' => $this->getName(),
            'handle' => $this->getHandle(),
            'primaryColor' => $this->getPrimaryColor(),
            'icon' => $this->getIcon(),
            'url' => $this->getUrl(),
        ]);
    }

    public function getName(): string
    {
        return $this->getProvider()->getName();
    }

    public function getHandle(): string
    {
        return $this->getProvider()->getHandle();
    }

    public function getPrimaryColor(): ?string
    {
        return $this->getProvider()->getPrimaryColor();
    }

    public function getIcon(): ?string
    {
        return $this->getProvider()->getIcon();
    }

    public function getUrl(): ?string
    {
        $options = $this->getRenderOptions();

        return $options['url'] ?? null;
    }

    public function getButtonAttributes(): array
    {
        return [
            'href' => $this->getUrl(),
            'aria-label' => $this->getName(),
            'target' => '_blank',
            'rel' => 'nofollow noopener noreferrer',
        ];
    }

    public function getProvider(): Provider
    {
        return $this->_provider;
    }

    public function setProvider($value): void
    {
        $this->_provider = $value;
    }

    public function getRenderOptions(): array
    {
        return $this->_renderOptions;
    }

    public function setRenderOptions($value): void
    {
        $this->_renderOptions = $value;
    }

    public function render(): ?Markup
    {
        $settings = SocialShare::$plugin->getSettings();
        $options = $this->getRenderOptions();

        // Setup attributes for each HTML element, from template overrides or defaults
        $buttonAttributes = $options['buttonAttributes'] ?? $settings->buttonAttributes;
        $iconWrapperAttributes = $options['iconWrapperAttributes'] ?? $settings->iconWrapperAttributes;
        $labelAttributes = $options['labelAttributes'] ?? $settings->labelAttributes;
        $labelWrapperAttributes = $options['labelWrapperAttributes'] ?? $settings->labelWrapperAttributes;
        $contentAttributes = $options['contentAttributes'] ?? $settings->contentAttributes;

        // Watch some other attributes we might need to merge by ensuring they're either set in templates, or arrays
        $buttonAttributes['data'] = $buttonAttributes['data'] ?? [];
        $iconWrapperAttributes['data'] = $iconWrapperAttributes['data'] ?? [];
        $labelAttributes['data'] = $labelAttributes['data'] ?? [];
        $labelWrapperAttributes['data'] = $labelWrapperAttributes['data'] ?? [];
        $contentAttributes['data'] = $contentAttributes['data'] ?? [];
        $buttonAttributes['style'] = $buttonAttributes['style'] ?? [];

        // Attributes can define their own tag to use
        $buttonTag = ArrayHelper::remove($buttonAttributes, 'tag', 'a');
        $iconWrapperTag = ArrayHelper::remove($iconWrapperAttributes, 'tag', 'span');
        $labelTag = ArrayHelper::remove($labelAttributes, 'tag', 'span');
        $labelWrapperTag = ArrayHelper::remove($labelWrapperAttributes, 'tag', 'span');
        $contentTag = ArrayHelper::remove($contentAttributes, 'tag', 'span');

        // Are we theming?
        $themeOptions = $options['theme'] ?? [];

        if ($themeOptions) {
            Craft::$app->getView()->registerAssetBundle(FrontEndAsset::class);

            $buttonAttributes['data'] = array_merge($buttonAttributes['data'], array_filter([
                'social-button' => true,
                'show-icon' => $themeOptions['showIcon'] ?? true,
                'show-label' => $themeOptions['showLabel'] ?? false,
                'shape' => $themeOptions['shape'] ?? 'rectangle',
                'bg' => $themeOptions['bgColor'] ?? 'brand',
            ]));

            $iconWrapperAttributes['data'] = array_merge($iconWrapperAttributes['data'], [
                'sb-icon-wrapper' => true,
            ]);

            $labelAttributes['data'] = array_merge($labelAttributes['data'], [
                'sb-label' => true,
            ]);

            $labelWrapperAttributes['data'] = array_merge($labelWrapperAttributes['data'], [
                'sb-label-wrapper' => true,
            ]);

            $contentAttributes['data'] = array_merge($contentAttributes['data'], [
                'sb-content' => true,
            ]);

            $buttonAttributes['style'] = array_merge($buttonAttributes['style'], array_filter([
                '--icon-color' => $this->_getThemeColor(($themeOptions['iconColor'] ?? 'white')),
                '--icon-hover-color' => $this->_getThemeColor(($themeOptions['iconHoverColor'] ?? 'white')),
                '--bg-color' => $this->_getThemeColor(($themeOptions['bgColor'] ?? 'brand')),
                '--bg-hover-color' => $this->_getThemeColor(($themeOptions['bgHoverColor'] ?? 'brand')),
            ]));
        } else {
            // Use CSS variables to tell the front-end about the brand color
            $buttonAttributes['style'] = array_merge($buttonAttributes['style'], [
                '--brand-color' => $this->getPrimaryColor(),
            ]);
        }

        // Build the HTML
        $iconWrapper = Html::tag($iconWrapperTag, $this->getIcon(), $iconWrapperAttributes);
        $label = Html::tag($labelTag, $this->getName(), $labelAttributes);
        $labelWrapper = Html::tag($labelWrapperTag, $label, $labelWrapperAttributes);
        $content = Html::tag($contentTag, $iconWrapper . $labelWrapper, $contentAttributes);

        return Template::raw(Html::tag($buttonTag, $content, array_merge($this->getButtonAttributes(), $buttonAttributes)));
    }


    // Private Methods
    // =========================================================================

    private function _getThemeColor(?string $color): ?string
    {
        if ($color === 'brand') {
            return $this->getPrimaryColor();
        }

        if ($color === 'black') {
            return '#000';
        }

        if ($color === 'white') {
            return '#fff';
        }

        return $color;
    }

}