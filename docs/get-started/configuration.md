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

**Type:** `mixed` · **Default:** `86400`

The number of seconds to cache. Default to 1 day.
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

Whether when clicking on a share button should open a modal window with the provider share URL. Disabling this will open the same link, just in a new tab.
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
                'appId' => '••••••••••••••••••••••••••••',
                'appSecret' => '••••••••••••••••••••••••••••',
            ],
        ],
    ],
];
```

