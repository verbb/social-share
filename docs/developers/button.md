# Button
Whenever you're dealing with a button in your template, you're actually working with a `Button` object.

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

::: reference
### `url`

**Type:** `string|null`

The URL, if passed in through `renderOptions`.
:::


## Methods

::: reference
### `getRenderOptions()`

**Returns:** `array`

Returns any render options passed in when creating the button.
:::

::: reference
### `getProvider()`

**Returns:** `verbb\socialshare\base\Provider`

Returns the provider the button is for.
:::

::: reference
### `render()`

**Returns:** `Twig\Markup|null`

Returns the rendered HTML for the button.
:::
