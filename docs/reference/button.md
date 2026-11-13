# Button

A button shows a provider’s icon and name, and links wherever you choose, such as your own profile on that platform. Whenever you’re dealing with a button in your template, you’re working with a `Button` object. Get one with `craft.socialShare.getButton()`, or render it straight away with `craft.socialShare.renderButton()`. For a button that shares the current page, use a [Share Button](docs:reference/share-button) instead.

```twig
{% set button = craft.socialShare.getButton('instagram', { url: 'https://www.instagram.com/example' }) %}

<a href="{{ button.url }}" style="color: {{ button.primaryColor }}">
    {{ button.icon | raw }} {{ button.name }}
</a>
```

<span id="attributes"></span>

## Properties

::: reference
### `name`

**Type:** `string`

The provider’s name, such as `Instagram`, used as the button’s label.
:::

::: reference
### `handle`

**Type:** `string`

The provider’s handle, such as `instagram`.
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
### `url`

**Type:** `string|null`

The link passed in the `url` render option, or `null` when none was passed.
:::

## Methods

::: reference
### `getRenderOptions()`

**Returns:** `array`

Returns the render options passed in when the button was created.
:::

::: reference
### `getProvider()`

**Returns:** `verbb\socialshare\base\Provider`

Returns the [Provider](docs:reference/provider) the button is for.
:::

::: reference
### `getButtonAttributes()`

**Returns:** `array`

Returns the HTML attributes for the button’s link, including `href`, `aria-label`, `target` and `rel`.
:::

::: reference
### `render()`

**Returns:** `Twig\Markup|null`

Returns the HTML for the button, using its render options and the plugin’s attribute settings.
:::
