# Provider
Whenever you're dealing with a provider in your template, you're actually working with a `Provider` object.

## Attributes

Attribute | Description
--- | ---
`name` | The name of the provider.
`handle` | The handle of the provider.
`primaryColor` | The primary brand color of the provider.
`icon` | The SVG icon of the provider.

## Methods

Method | Description
--- | ---
`getShareUrl(url, text, params)` | The share URL unique to each provider.
`getShareButton(options)` | Returns the [Share Button](docs:developers/share-button).
`renderShareButton(options)` | Returns the rendered HTML for the [Share Button](docs:developers/share-button).
`getButton(options)` | Returns the [Button](docs:developers/button).
`renderButton(options)` | Returns the rendered HTML for the [Button](docs:developers/button).
