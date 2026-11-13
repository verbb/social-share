# Share Button

A share button links to a provider’s share page for a URL, so visitors can share the current page or any URL you choose. Whenever you’re dealing with a share button in your template, you’re working with a `ShareButton` object. Get one with `craft.socialShare.getShareButton()`, or render it straight away with `craft.socialShare.renderShareButton()`.

```twig
{% set share = craft.socialShare.getShareButton('facebook', { url: entry.url, text: entry.title }) %}

<a href="{{ share.providerUrl }}" target="_blank" rel="noopener">
    Share on {{ share.name }}
</a>
```

A share button has every property and method of a [Button](docs:reference/button), plus the ones below.

<span id="attributes"></span>

## Properties

::: reference
### `providerUrl`

**Type:** `string|null`

The provider’s share link, built from the `url`, `text` and `params` render options. Without a `url` option, it shares the current page’s URL. This is `null` when there’s no URL to share or the provider can’t share.
:::

## Methods

::: reference
### `getButtonAttributes()`

**Returns:** `array`

Returns the HTML attributes for the button’s link, with `href` set to the share link. When the `useModalForShare` setting is on, it also adds the attributes that open the share page in a popup.
:::
