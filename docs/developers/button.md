# Button
Whenever you're dealing with a button in your template, you're actually working with a `Button` object.

## Attributes

Attribute | Description
--- | ---
`name` | The name of the provider.
`handle` | The handle of the provider.
`primaryColor` | The primary brand color of the provider.
`icon` | The SVG icon of the provider.
`url` | The URL, if passed in through `renderOptions`.

## Methods

Method | Description
--- | ---
`getRenderOptions()` | Returns any render options passed in when creating the button.
`getProvider()` | Returns the provider the button is for.
`render()` | Returns the rendered HTML for the button.
