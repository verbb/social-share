# Share Counts
Showing how many times a given URL has been shared on social media can do wonders to boost the reputation of your content. For example, you may have a popular blog post that you know is being shared on Facebook. You can query the Facebook API, with a given URL to see how many times anyone on Facebook has shared that exact URL.

Share counts are heavily cached to prevent slow page loading, and triggering API's. This is controlled with the `cacheDuration` plugin setting.

The following providers support fetching share counts:

| Provider | Requirements |
| - | - |
| Buffer | No account configuration is required. |
| Facebook | Configure the provider with your Facebook App ID and App Secret. |
| Pinterest | No account configuration is required. |
| Tumblr | No account configuration is required. |

## Getting Share Count Providers
You can fetch all providers that support share counts. This will return a collection of [Provider](docs:developers/provider) objects.

```twig
{% for provider in craft.socialShare.getSharesCountProviders() %}
    {{ provider.name }}
{% endfor %}
```

## Getting Share Counts
You'll also want to fetch the counts for a page. Call `getShares()` with the provider and a stable, canonical URL that identifies the content. For an entry, use its URL directly:

```twig
{{ craft.socialShare.getShares('facebook', entry.url) }}
```

This should return the total number of times the entry has been shared on Facebook (e.g. `54.6K`).

Pass the URL explicitly. Avoid using `craft.app.request.absoluteUrl` when it contains tracking or other visitor-specific query parameters, because providers and Social Share treat each complete URL as a separate count and cache lookup.

```twig
{% set url = entry.url %}

{{ craft.socialShare.getShares('facebook', url) }}
```

But you can also use a completely arbitrary URL - it doesn't even have to be from your website.

```twig
{{ craft.socialShare.getShares('facebook', 'https://verbb.io') }}
```

### Render Options
You can pass in a number of options to control output.

```twig
{{ craft.socialShare.getShares('facebook', entry.url, {
    enableCache: true,
    cacheDuration: 3600,
    friendlyCount: false,
}) }}
```

### Minimum Count
You can set a minimum count, so that the value won't show unless it's over this limit. This can be useful to prevent showing a low number of shares, which may reflect poorly on the page in question, and harm its reputation.

Setting the `minShareCount` plugin setting will return `null` for any value that is under that limit.

### Friendly Numbers
By default, Social Share will convert the raw number (e.g. `54624`) to a "friendlier" abbreviated notation like `54.6K`. You can control this via the `friendlyCount` plugin setting, or by passing this in as an option when rendering.

```twig
{{ craft.socialShare.getShares('facebook', entry.url, {
    friendlyCount: false,
}) }}

{# Would render... #}
87372
```
