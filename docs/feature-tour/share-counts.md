# Share Counts
Showing how many times a given URL has been shared on social media can do wonders to boost the reputation of your content. For example, you may have a popular blog post that you know is being shared on Facebook. You can query the Facebook API, with a given URL to see how many times anyone on Facebook has shared that exact URL.

Share counts are heavily cached to prevent slow page loading, and triggering API's. This is controlled with the `cacheDuration` plugin setting.

The following providers support fetching share counts:

- Buffer
- Facebook
- LinkedIn
- Pinterest
- Reddit
- Tumblr
- X (Twitter)
- Yummly

## Getting Share Count Providers
You can fetch all providers that support share counts. This will return a collection of [Provider](docs:developers/provider) objects.

```twig
{% for provider in craft.socialShare.getSharesCountProviders() %}
    {{ provider.name }}
{% endfor %}
```

## Getting Share Counts
You'll also want to fetch the counts for the current page. You can do so by calling `getShares()` for the provider you want to check against. Let's fetch share counts for the current page from Facebook.

```twig
{{ craft.socialShare.getShares('facebook') }}
```

This should return the total number of times the current page has been shared on Facebook (e.g. `54.6K`).

The current URL will be used, but you can change this to be whatever URL you require. For example, this is the same as what Social Share does by default, but omitting the second parameter:

```twig
{% set url = craft.app.request.absoluteUrl %}

{{ craft.socialShare.getShares('facebook', url) }}
```

And if you're on an entry, you could use the entry URL:

```twig
{{ craft.socialShare.getShares('facebook', entry.url) }}
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

