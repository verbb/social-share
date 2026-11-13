# Displaying Social Counts

Request the count for the intended account or URL. The account identifier depends on the provider, so use the relevant provider setup rather than assuming every service accepts the same username format.

For example, a site footer might show how many people follow your YouTube channel, while an article template shows how many times the article has been shared on Facebook.

```twig
{% set subscribers = craft.socialShare.getFollowers('youTube', '@craftcms') %}

{% if subscribers %}
    <p>{{ subscribers }} subscribers on YouTube</p>
{% endif %}

{% set shares = craft.socialShare.getShares('facebook', entry.url) %}

{% if shares %}
    <p>Shared {{ shares }} times</p>
{% endif %}
```

Both counts are formatted as friendly numbers, such as `1.2K`, when [`friendlyCount`](docs:get-started/configuration) is enabled. Each count is cached for the [`cacheDuration`](docs:get-started/configuration), so the provider is only asked again once the cache expires. Check the result before displaying it, because a count can be `null` when the provider can't return one.

## Showing Follower Counts

`craft.socialShare.getFollowers(handle, account, options)` returns the number of followers or subscribers an account has on a provider, or `null` when the provider doesn't support follower counts, isn't configured or can't return a count. `handle` is the provider's handle and `account` is the identifier the provider expects, such as a username, page name or channel ID. Both are required. [Follower Counts](docs:feature-tour/follower-counts) lists the supported providers and what each needs. Dribbble and Instagram always return the count for the account connected in the plugin settings, whatever `account` you pass.

`options` is an optional hash that overrides the plugin settings for this call. It accepts `enableCache`, `cacheDuration` and `friendlyCount`.

```twig
{# Show the exact number, refreshed every 6 hours #}
{{ craft.socialShare.getFollowers('gitHub', 'verbb', {
    friendlyCount: false,
    cacheDuration: 'PT6H',
}) }}
```

## Showing Share Counts

`craft.socialShare.getShares(handle, url, options)` returns the number of times a URL has been shared on a provider, or `null` when the provider doesn't support share counts or can't return a count. `handle` is the provider's handle and `url` is the full URL of the page. Both are required. Use `entry.url` on an entry template. [Share Counts](docs:feature-tour/share-counts) lists the supported providers.

`options` accepts the same `enableCache`, `cacheDuration` and `friendlyCount` keys as `getFollowers()`. The result is also `null` when the count is below the [`minShareCount`](docs:get-started/configuration) setting, which lets you hide low numbers.

```twig
{{ craft.socialShare.getShares('pinterest', entry.url, {
    enableCache: false,
}) }}
```
