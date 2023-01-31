# Configuration
Create a `social-share.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Social Share, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'enableCache' => true,
        'cacheDuration' => 86400,
        'friendlyCount' => true,
        'minShareCount' => null,
        'useModalForShare' => true,
        'buttonAttributes' => [],
        'iconWrapperAttributes' => [],
        'labelAttributes' => [],
        'labelWrapperAttributes' => [],
        'contentAttributes' => [],
        'providers' => [],
    ]
];
```

## Configuration Options
- `enableCache` - Whether share and follower counts should be cached. Only disable this for local debugging.
- `cacheDuration` - The number of seconds to cache. Default to 1 day.
- `friendlyCount` - Whether share and follower counts should be shown as "friendly" and abbreviated. For example, `12345` shown as `12.3k`.
- `minShareCount` - Set the minimum number of shares that must be met in order to show a value. This can help to prevent showing low-shares.
- `useModalForShare` - Whether when clicking on a share button should open a modal window with the provider share URL. Disabling this will open the same link, just in a new tab.
- `buttonAttributes` - A collection of HTML attributes to be added to the button HTML element.
- `iconWrapperAttributes` - A collection of HTML attributes to be added to the button's icon wrapper HTML element.
- `labelAttributes` - A collection of HTML attributes to be added to the button's label HTML element.
- `labelWrapperAttributes` - A collection of HTML attributes to be added to the button's label wrapper HTML element.
- `contentAttributes` - A collection of HTML attributes to be added to the button's wrapper HTML element.
- `providers` - A collection of settings for a provider.

## Provider Settings
You can set provider settings by adding the `handle` of a provider, and passing in any setting specific to that provider.

```php
'providers' => [
    'facebook' => [
        'appId' => 'xxxxxxxxxxxx',
        'appSecret' => 'xxxxxxxxxxxx',
    ],
],
```

