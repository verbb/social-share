# Follower Counts
Similar to [Share Counts](docs:feature-tour/share-counts), you can also fetch the number of followers or subscribers a particular user has on a social media platform. For example, we can query how many subscribers a YouTube channel has, or how many people follow you on Pinterest.

Follower counts are heavily cached to prevent slow page loading, and triggering API's. This is controlled with the `cacheDuration` plugin setting.

The following providers support fetching follower counts:

| Provider | Requirements |
| - | - |
| [Dribbble](docs:providers/dribbble) | Configure and connect the provider under **Settings → Social Share → Providers**. |
| [Envato](docs:providers/envato) | Configure a personal token. |
| Facebook | No account configuration is required. |
| Feedly | No account configuration is required. |
| GitHub | No account configuration is required. |
| [Instagram](docs:providers/instagram) | Configure and connect the provider under **Settings → Social Share → Providers**. |
| [Mailchimp](docs:providers/mailchimp) | Configure an API key. |
| MixCloud | No account configuration is required. |
| Pinterest | No account configuration is required. |
| SoundCloud | No account configuration is required. |
| Steam | No account configuration is required. |
| Vkontakte | No account configuration is required. |
| [X (Twitter)](docs:providers/twitter) | Configure the provider with your Client ID and Client Secret. |
| YouTube | Pass an `@handle` or a channel ID beginning with `UC`. |

Provider credentials are configured under **Settings → Social Share → Providers**. Environment variables keep credential values out of project config.

## Getting Follower Count Providers
You can fetch all providers that support follower counts. This will return a collection of [Provider](docs:developers/provider) objects.

```twig
{% for provider in craft.socialShare.getFollowersCountProviders() %}
    {{ provider.name }}
{% endfor %}
```

## Getting Follower Counts
You'll also want to fetch the counts for the social media platform. You can do so by calling `getFollowers()` for the provider you want to check against. You'll also need to provide the username, channel or other identifier for the platform. Let's fetch follower counts for a user on Facebook.

```twig
{{ craft.socialShare.getFollowers('facebook', 'craftcms') }}
```

This should return the total number of followers for `craftcms` (e.g. `2K`).

As each platform is different, use the guide below for the value passed as the second parameter:

- Facebook — page ID or username, such as `craftcms`.
- Pinterest — username, such as `craftcms`.
- YouTube — handle, such as `@craftcms`, or channel ID, such as `UC45X_OuS3I2Kq7ILkS5A1uw`.

Dribbble and Instagram return the follower count for the connected account. Their `account` argument is still required for call compatibility, but its value does not select another account.

### Render Options
You can pass in a number of options to control output.

```twig
{{ craft.socialShare.getFollowers('facebook', 'craftcms', {
    enableCache: true,
    cacheDuration: 3600,
    friendlyCount: false,
}) }}
```

### Friendly Numbers
By default, Social Share will convert the raw number (e.g. `54624`) to a "friendlier" abbreviated notation like `54.6K`. You can control this via the `friendlyCount` plugin setting, or by passing this in as an option when rendering.

```twig
{{ craft.socialShare.getFollowers('facebook', 'craftcms', {
    friendlyCount: false,
}) }}

{# Would render... #}
87372
```
