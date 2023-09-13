# Follower Counts
Similar to [Share Counts](docs:feature-tour/share-counts), you can also fetch the number of followers or subscribers a particular user has on a social media platform. For example, we can query how many subscribers a YouTube channel has, or how many people follow you on Pinterest.

Follower counts are heavily cached to prevent slow page loading, and triggering API's. This is controlled with the `cacheDuration` plugin setting.

The following providers support fetching follower counts:

- Behance
- Dribbble
- Envato
- Facebook
- Feedly
- GitHub
- Instagram
- Mailchimp
- MixCloud
- Pinterest
- SoundCloud
- Spotify
- Steam
- Vimeo
- Vkontakte
- X (Twitter)
- YouTube Channel
- YouTube User

## Getting Follower Count Providers
You can fetch all providers that support follower counts. This will return a collection of [Provider](docs:developers/provider) objects.

```twig
{% for provider in craft.socialShare.getFollowerCountProviders() %}
    {{ provider.name }}
{% endfor %}
```

## Getting Follower Counts
You'll also want to fetch the counts for the social media platform. You can do so by calling `getFollowers()` for the provider you want to check against. You'll also need to provide the username, channel or other identifier for the platform. Let's fetch follower counts for a user on Facebook.

```twig
{{ craft.socialShare.getFollowers('facebook', 'craftcms') }}
```

This should return the total number of followers for `craftcms` (e.g. `2K`).

As each platform is different, you can use the guide below for what value the second parameter needs to be:

- Facebook - Page ID, or Username (e.g. `xxxxxxxxx` or `craftcms`)
- Pinterest - Username (e.g. `craftcms`)
- YouTube User - Username (e.g. `@craftcms`)
- YouTube Channel - Channel ID (e.g. `xxxxxxxxx`)

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

