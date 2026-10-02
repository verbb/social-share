# Configuration

You can customise Social Share’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `social-share.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will disable abbreviated share counts:

```php
<?php

return [
    'friendlyCount' => false,
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `enableCache`

**Type:** `bool` · **Default:** `true`

Whether share and follower counts should be cached. Only disable this for local debugging.
:::

::: reference
### `cacheDuration`

**Type:** `int|string|DateInterval|null` · **Default:** `86400`

The cache duration as a number of seconds, an ISO 8601 duration string or a `DateInterval` object. Set this to `0` or `null` to cache indefinitely. Defaults to one day.
:::

::: reference
### `graphqlNegativeCacheDuration`

**Type:** `int` · **Default:** `60`

The maximum number of seconds that GraphQL caches a failed, zero or otherwise non-positive share or follower count. A short duration prevents unsuccessful lookups from occupying the count cache for the full `cacheDuration` while still avoiding immediate repeated provider requests.
:::

::: reference
### `graphqlOperationRequestLimit`

**Type:** `int` · **Default:** `20`

The maximum number of uncached share- or follower-count lookups one GraphQL operation can trigger. Cached count fields do not consume this budget.
:::

::: reference
### `graphqlAggregateRequestLimit`

**Type:** `int` · **Default:** `60`

The maximum number of uncached share- or follower-count lookups that GraphQL can trigger across all providers during the configured request window. This site-wide budget is applied before the existing per-provider budget.
:::

::: reference
### `graphqlProviderRequestLimit`

**Type:** `int` · **Default:** `60`

The maximum number of outbound share- or follower-count lookups that GraphQL can trigger for each provider during the configured request window. Share and follower lookups consume the same provider budget, and one lookup can involve more than one HTTP request when required by the provider. Cached GraphQL responses and requests made from Twig do not consume this budget.

The budget uses Craft’s configured cache and mutex components. The cache must retain written values; GraphQL cache misses fail closed when the budget cannot be persisted. Multi-node deployments must configure both components with a coherent shared backend to enforce one site-wide budget; node-local components enforce the limit independently on each node.
:::

::: reference
### `graphqlProviderRequestWindow`

**Type:** `int` · **Default:** `60`

The number of seconds in each GraphQL aggregate and provider request window.
:::

::: reference
### `friendlyCount`

**Type:** `bool` · **Default:** `true`

Whether share and follower counts should be shown as "friendly" and abbreviated. For example, `12345` shown as `12.3k`.
:::

::: reference
### `minShareCount`

**Type:** `int|null` · **Default:** `null`

Set the minimum number of shares that must be met in order to show a value. This can help to prevent showing low-shares.
:::

::: reference
### `useModalForShare`

**Type:** `bool` · **Default:** `true`

Whether an ordinary left-click on a rendered share button should open the provider share URL in a popup window. Social Share implements the popup with a bundled external script, while keeping the real provider URL in the link so it still works when JavaScript is unavailable. Modified clicks retain the browser’s normal new-tab or new-window behaviour. Disabling this setting always opens the link in a new tab.
:::

::: reference
### `buttonAttributes`

**Type:** `array` · **Default:** `[]`

A collection of HTML attributes to be added to the button HTML element.
:::

::: reference
### `iconWrapperAttributes`

**Type:** `array` · **Default:** `[]`

A collection of HTML attributes to be added to the button's icon wrapper HTML element.
:::

::: reference
### `labelAttributes`

**Type:** `array` · **Default:** `[]`

A collection of HTML attributes to be added to the button's label HTML element.
:::

::: reference
### `labelWrapperAttributes`

**Type:** `array` · **Default:** `[]`

A collection of HTML attributes to be added to the button's label wrapper HTML element.
:::

::: reference
### `contentAttributes`

**Type:** `array` · **Default:** `[]`

A collection of HTML attributes to be added to the button's wrapper HTML element.
:::

::: reference
### `providers`

**Type:** `array` · **Default:** `[]`

A collection of settings for a provider.
:::


## Provider Settings
You can set provider settings by adding the `handle` of a provider, and passing in any setting specific to that provider.

```php
return [
    '*' => [
        // ...
        'providers' => [
            'facebook' => [
                'clientId' => '$SOCIAL_SHARE_FACEBOOK_CLIENT_ID',
                'clientSecret' => '$SOCIAL_SHARE_FACEBOOK_CLIENT_SECRET',
            ],
        ],
    ],
];
```
