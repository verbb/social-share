# Events

Social Share provides events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to change Social Share’s behaviour.

## Provider Events

### The `registerProviderTypes` Event
The event that is triggered when Social Share builds the list of available providers. Append your class to `$event->types` to register it. [Custom Providers](docs:developers/custom-providers) shows a complete provider class.

```php
use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\LobstersProvider;
use verbb\socialshare\services\Providers;
use yii\base\Event;

Event::on(Providers::class, Providers::EVENT_REGISTER_PROVIDER_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = LobstersProvider::class;
});
```
