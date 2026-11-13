# Choosing Share Providers

Choose providers according to the feature you need. A provider that creates share links may not support follower or share counts, so use the corresponding capability-specific collection.

For example, an article template might offer a share button for every provider that supports sharing, without listing each one by hand.

```twig
{% for provider in craft.socialShare.getShareButtonProviders() %}
    {{ craft.socialShare.renderShareButton(provider.handle, {
        theme: {
            showLabel: true,
        },
    }) }}
{% endfor %}
```

Each provider has a `name`, `handle`, `primaryColor` and `icon`, so you can also build your own markup from these lists. [Rendering Social Buttons](docs:template-guides/rendering-social-buttons) covers the button calls.

## Listing Providers

`craft.socialShare.getProviders()` returns an array of every [Provider](docs:reference/provider) object Social Share supports, whatever it can do. It takes no arguments. Use it when you're building ordinary profile [Buttons](docs:feature-tour/buttons), which every provider supports.

## Getting One Provider

`craft.socialShare.getProvider(handle)` returns the [Provider](docs:reference/provider) with the given handle, or `null` if there isn't one. `handle` is required and must match exactly, such as `facebook` or `linkedIn`. Use it to read a single provider's details, such as its brand colour.

```twig
{% set facebook = craft.socialShare.getProvider('facebook') %}

{% if facebook %}
    <p style="color: {{ facebook.primaryColor }}">Follow us on {{ facebook.name }}</p>
{% endif %}
```

## Listing Providers by Feature

`craft.socialShare.getFollowersCountProviders()` returns an array of the [Provider](docs:reference/provider) objects that support [Follower Counts](docs:feature-tour/follower-counts). It takes no arguments. Some of these need credentials before they return a count, as listed on that page.

`craft.socialShare.getSharesCountProviders()` returns an array of the [Provider](docs:reference/provider) objects that support [Share Counts](docs:feature-tour/share-counts). It takes no arguments.

`craft.socialShare.getShareButtonProviders()` returns an array of the [Provider](docs:reference/provider) objects that support [Share Buttons](docs:feature-tour/share-buttons). It takes no arguments.
