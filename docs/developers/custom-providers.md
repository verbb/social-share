# Custom Providers

A provider represents one social media platform, and gives Social Share a brand colour and icon for its buttons. A provider can also opt in to share buttons, follower counts and share counts. Social Share includes over 100 providers, and a custom provider adds a platform it doesn’t cover, or changes how an existing one works by extending its class.

## Create the Provider Class

Extend `verbb\socialshare\base\Provider`. This example adds a share button for Lobsters. Create it at `modules/sitemodule/LobstersProvider.php` in a module autoloaded as `modules\sitemodule`.

```php
<?php
namespace modules\sitemodule;

use craft\helpers\UrlHelper;
use verbb\socialshare\base\Provider;

class LobstersProvider extends Provider
{
    public static string $handle = 'lobsters';

    public static function displayName(): string
    {
        return 'Lobsters';
    }

    public static function supportsShareButton(): bool
    {
        return true;
    }

    public function getPrimaryColor(): ?string
    {
        return '#ac130d';
    }

    public function getIcon(): ?string
    {
        return '<svg viewBox="0 0 24 24" fill="currentColor"><rect width="24" height="24" rx="4"/></svg>';
    }

    public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
    {
        return UrlHelper::urlWithParams('https://lobste.rs/stories/new', array_filter(array_merge([
            'url' => $url,
            'title' => $text,
        ], $params)));
    }
}
```

Every provider needs a primary colour and an icon, because every provider can render a [Button](docs:reference/button). `$handle` identifies the provider in templates, such as `craft.socialShare.renderShareButton('lobsters')`. Replace the placeholder square in `getIcon()` with the platform’s own SVG icon.

## Register the Provider

Social Share finds providers through the `registerProviderTypes` event. Register your class from your module or plugin’s `init()` method.

```php
use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\LobstersProvider;
use verbb\socialshare\services\Providers;
use yii\base\Event;

Event::on(Providers::class, Providers::EVENT_REGISTER_PROVIDER_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = LobstersProvider::class;
});
```

Render a share button in any template to check it works. The button should use your colour and icon, and link to Lobsters with the current page’s URL.

```twig
{{ craft.socialShare.renderShareButton('lobsters', { text: entry.title }) }}
```

## Add Counts

A provider can also report follower counts and share counts. Each needs a static method that opts in, and a method that fetches the number.

### Support Follower Counts
If your provider supports fetching the number of followers for a given account identifier, you can configure the following:

```php
public static function supportsFollowersCount(): bool
{
    return true;
}

public function getFollowersCount(string $account): ?int
{
    // Your logic to fetch the followers count
}
```

### Support Share Counts
If your provider supports fetching the number of shares for a given URL, you can configure the following:

```php
public static function supportsSharesCount(): bool
{
    return true;
}

public function getSharesCount(string $url): ?int
{
    // Your logic to fetch the shares count
}
```

### Log Provider Errors
If a provider API request fails, log it with `verbb\socialshare\helpers\ProviderLog`, which records the provider handle and exception class without credentials or request data. Import `ProviderLog` and `Throwable` at the top of your class.

```php
try {
    // Your logic to fetch the count
} catch (Throwable $exception) {
    ProviderLog::apiError($this, $exception);

    return null;
}
```

Don’t log raw exception messages or traces, access tokens, client secrets, OAuth codes or state, response bodies, request headers, or complete URLs and query strings. `ProviderLog` is separate from the provider base class so it doesn’t conflict with custom provider methods. Custom providers can keep returning `null` after a failed count request so templates retain the built-in failure behaviour.

## Methods

Override these methods in your provider class as needed. Only `getPrimaryColor()` and `getIcon()` are needed for every provider, unless `$handle` matches a provider Auth already knows.

::: reference
### `displayName()`

**Returns:** `string`

Returns the provider name shown in the control panel and as the button label. This is a static method.
:::

::: reference
### `getPrimaryColor()`

**Returns:** `string|null`

Returns the provider’s brand colour as a hex value, used for button backgrounds and the `--brand-color` CSS variable. The default looks up `$handle` in Auth’s list of known providers.
:::

::: reference
### `getIcon()`

**Returns:** `string|null`

Returns the provider’s icon as SVG markup. The default looks up `$handle` in Auth’s list of known providers.
:::

::: reference
### `supportsShareButton()`

**Returns:** `bool`

Returns whether the provider can share a URL, so it’s offered for share buttons. This is a static method.
:::

::: reference
### `getShareUrl()`

**Arguments:** `url` (`string`), the page to share, `text` (`string|null`), optional text to share with it, and `params` (`array`), extra query parameters passed in a template’s `params` option.

**Returns:** `string|null`

Returns the URL that shares a page on the provider, or `null` when the provider can’t share.
:::

::: reference
### `supportsFollowersCount()`

**Returns:** `bool`

Returns whether the provider can fetch an account’s follower count. This is a static method.
:::

::: reference
### `getFollowersCount()`

**Arguments:** `account` (`string`), the account identifier passed from the template.

**Returns:** `int|null`

Returns the number of followers for an account, or `null` when it can’t be fetched.
:::

::: reference
### `supportsSharesCount()`

**Returns:** `bool`

Returns whether the provider can fetch how many times a URL has been shared. This is a static method.
:::

::: reference
### `getSharesCount()`

**Arguments:** `url` (`string`), the URL to count shares for.

**Returns:** `int|null`

Returns the number of times a URL has been shared on the provider, or `null` when it can’t be fetched.
:::

::: reference
### `hasSettings()`

**Returns:** `bool`

Returns whether the provider has settings to edit in the control panel, such as API credentials. This is a static method.
:::

::: reference
### `getSettingsHtml()`

**Returns:** `string|null`

Returns the settings shown when editing the provider in the control panel.
:::

::: reference
### `isConfigured()`

**Returns:** `bool`

Returns whether the provider has the settings it needs, such as API credentials, to fetch counts.
:::

::: reference
### `getButtonAttributes()`

**Arguments:** `attributes` (`array`), the default attributes, including `href`, `aria-label`, `target` and `rel`.

**Returns:** `array`

Returns the HTML attributes for the provider’s buttons. Override it to add or change attributes, starting from the array passed in.
:::
