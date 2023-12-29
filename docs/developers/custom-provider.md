# Custom Provider
You can register your own Provider to add support for other social media platforms, or even extend an existing Provider.

```php
namespace modules\sitemodule;

use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\MyProvider;
use verbb\socialshare\services\Providers;
use yii\base\Event;

Event::on(Providers::class, Providers::EVENT_REGISTER_PROVIDER_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = MyProvider::class;
});
```

## Example
Create the following class to house your Provider logic.

```php
<?php
namespace modules\sitemodule;

class MyProvider extends Provider
{
    // Properties
    // =========================================================================

    public static string $handle = 'myProvider';


    // Public Methods
    // =========================================================================

    public function getPrimaryColor(): ?string
    {
        return '#000000';
    }

    public function getIcon(): ?string
    {
        return '<svg ...';
    }
}
```

This is the minimum amount of implementation required, as all Providers are required to have a button created. To do this, you'll need to define the primary colour and an SVG icon.

There are 3 other types of functionality your provider can opt-into, configured with the following:

### Supporting Followers Counts
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

### Supporting Share Counts
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

### Supporting Share Button
If your provider supports having content shared to it, you can configure the following:

```php
public static function supportsShareButton(): bool
{
    return true;
}

public function getShareUrl(string $url, ?string $text = null, array $params = []): ?string
{
    return UrlHelper::urlWithParams('https://my-provider.com/sharer.php', array_filter(array_merge([
        'url' => $url,
        'text' => $text,
    ], $params)));
}
```