# Provider

A provider represents one social media platform, such as Facebook or LinkedIn. It holds the platform’s name, brand colour and icon, and creates the buttons and share links for it. Get a provider by its handle, or loop over the providers that support a feature.

```twig
{% for provider in craft.socialShare.getShareButtonProviders() %}
    {{ provider.renderShareButton({ url: entry.url }) }}
{% endfor %}

{% set facebook = craft.socialShare.getProvider('facebook') %}
<a href="{{ facebook.getShareUrl(entry.url) }}">Share on {{ facebook.name }}</a>
```

<span id="attributes"></span>

## Properties

::: reference
### `name`

**Type:** `string`

The provider’s name, such as `Facebook`, used as the button label. This reads `getName()`.
:::

::: reference
### `handle`

**Type:** `string`

The provider’s handle, such as `facebook`, used to fetch it in templates.
:::

::: reference
### `primaryColor`

**Type:** `string|null`

The provider’s brand colour as a hex value, or `null` when it has none.
:::

::: reference
### `icon`

**Type:** `string|null`

The provider’s icon as SVG markup, or `null` when it has none.
:::

::: reference
### `cpEditUrl`

**Type:** `string|null`

The URL to edit the provider’s settings in the control panel.
:::

## Methods

::: reference
### `isConfigured()`

**Returns:** `bool`

Returns whether the provider has the settings it needs, such as API credentials, to fetch follower or share counts.
:::

::: reference
### `getShareUrl()`

**Arguments:** `url` (`string`), the page to share, `text` (`string|null`, optional), text to share with it, and `params` (`array`, optional), extra query parameters for the link.

**Returns:** `string|null`

Returns the link that shares a URL on the provider, or `null` when the provider doesn’t support sharing.
:::

::: reference
### `getShareButton()`

**Arguments:** `options` (`array`, optional), the [render options](docs:template-guides/rendering-buttons#render-options) such as `url`, `text`, `params` and `theme`.

**Returns:** `verbb\socialshare\models\ShareButton|null`

Returns a [Share Button](docs:reference/share-button) for the provider, without rendering it.
:::

::: reference
### `renderShareButton()`

**Arguments:** `options` (`array`, optional), the [render options](docs:template-guides/rendering-buttons#render-options) such as `url`, `text`, `params` and `theme`.

**Returns:** `Twig\Markup|null`

Returns the HTML for a [Share Button](docs:reference/share-button) for the provider.
:::

::: reference
### `getButton()`

**Arguments:** `options` (`array`, optional), the [render options](docs:template-guides/rendering-buttons#render-options) such as `url`, `text`, `params` and `theme`.

**Returns:** `verbb\socialshare\models\Button|null`

Returns a [Button](docs:reference/button) for the provider, without rendering it.
:::

::: reference
### `renderButton()`

**Arguments:** `options` (`array`, optional), the [render options](docs:template-guides/rendering-buttons#render-options) such as `url`, `text`, `params` and `theme`.

**Returns:** `Twig\Markup|null`

Returns the HTML for a [Button](docs:reference/button) for the provider.
:::
