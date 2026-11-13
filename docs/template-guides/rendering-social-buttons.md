# Rendering Social Buttons

Choose a share button when visitors should share a page, or an ordinary button when linking to a profile or destination. Fetch an object for custom markup, or use its rendering helper for generated HTML.

For example, an article template might offer share buttons for the article, and the site footer might link to your profiles.

```twig
{# Share this article #}
{% for handle in ['facebook', 'linkedIn', 'email'] %}
    {{ craft.socialShare.renderShareButton(handle, {
        url: entry.url,
        text: entry.title,
        theme: {
            showLabel: true,
        },
    }) }}
{% endfor %}

{# Follow us #}
{{ craft.socialShare.renderButton('instagram', {
    url: 'https://www.instagram.com/verbb',
    theme: {
        shape: 'circle',
    },
}) }}
```

The share buttons open each provider's share page for the article, and the Instagram button links to the profile. Both use Social Share's theme, which loads its own styles. [Rendering Buttons](docs:template-guides/rendering-buttons) covers the attribute and theme options in detail.

## Rendering Share Buttons

`craft.socialShare.getShareButton(handle, options)` returns a [Share Button](docs:reference/share-button) for the given provider, or `null` if there's no provider with that handle. `handle` is required. Use it when you want to write the button's markup yourself.

`options` is an optional hash. `url` is the page to share and defaults to the current page. `text` is an optional message to share with it, and `params` is a hash of extra query parameters for the provider's share URL. The returned object's `getProviderUrl()` method gives you the share link.

```twig
{% set button = craft.socialShare.getShareButton('linkedIn', { url: entry.url }) %}

{% if button %}
    <a href="{{ button.getProviderUrl() }}" target="_blank" rel="nofollow noopener noreferrer" style="color: {{ button.primaryColor }}">
        {{ button.icon|raw }} Share on {{ button.name }}
    </a>
{% endif %}
```

[Share Buttons](docs:feature-tour/share-buttons) lists the providers that support sharing.

`craft.socialShare.renderShareButton(handle, options)` outputs the HTML for a share button, or nothing if there's no provider with that handle. `handle` is required. `options` accepts the same `url`, `text` and `params` keys as `getShareButton()`, along with the rendering options described in [Rendering Buttons](docs:template-guides/rendering-buttons), such as `theme` and `buttonAttributes`.

```twig
{{ craft.socialShare.renderShareButton('facebook', { url: entry.url }) }}
```

When [`useModalForShare`](docs:get-started/configuration) is enabled, share buttons open the provider in a popup window instead of a new tab.

## Rendering Profile Buttons

`craft.socialShare.getButton(handle, options)` returns a [Button](docs:reference/button) for the given provider, or `null` if there's no provider with that handle. `handle` is required. An ordinary button is a styled link to any URL, such as your profile. Set it with the `url` option, because there's no default.

```twig
{% set button = craft.socialShare.getButton('gitHub', { url: 'https://github.com/verbb' }) %}

{% if button %}
    <a href="{{ button.url }}">{{ button.icon|raw }} {{ button.name }}</a>
{% endif %}
```

`craft.socialShare.renderButton(handle, options)` outputs the HTML for an ordinary button, or nothing if there's no provider with that handle. `handle` is required. `options` accepts `url` and the same rendering options as `renderShareButton()`.

```twig
{{ craft.socialShare.renderButton('youTube', {
    url: 'https://www.youtube.com/@craftcms',
}) }}
```
