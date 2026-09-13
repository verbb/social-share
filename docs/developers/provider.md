# Provider
Whenever you're dealing with a provider in your template, you're actually working with a `Provider` object.

<span id="attributes"></span>

## Properties

::: reference
### `name`

**Type:** `string`

The name of the provider.
:::

::: reference
### `handle`

**Type:** `string`

The handle of the provider.
:::

::: reference
### `primaryColor`

**Type:** `string|null`

The primary brand color of the provider.
:::

::: reference
### `icon`

**Type:** `string|null`

The SVG icon of the provider.
:::


## Methods

::: reference
### `getShareUrl(url, text, params)`

**Returns:** `string|null`

The share URL unique to each provider.
:::

::: reference
### `getShareButton(options)`

**Returns:** `verbb\socialshare\models\ShareButton|null`

Returns the [Share Button](docs:developers/share-button).
:::

::: reference
### `renderShareButton(options)`

**Returns:** `Twig\Markup|null`

Returns the rendered HTML for the [Share Button](docs:developers/share-button).
:::

::: reference
### `getButton(options)`

**Returns:** `verbb\socialshare\models\Button|null`

Returns the [Button](docs:developers/button).
:::

::: reference
### `renderButton(options)`

**Returns:** `Twig\Markup|null`

Returns the rendered HTML for the [Button](docs:developers/button).
:::
