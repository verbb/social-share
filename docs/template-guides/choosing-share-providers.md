# Choosing Share Providers

Choose providers according to the feature you need. A provider that creates share links may not support follower or share counts, so use the corresponding capability-specific collection.

## Calls Used in This Task

### `craft.socialShare.getProviders()`
Returns a collection of all [Provider](docs:developers/provider) objects.

### `craft.socialShare.getProvider(handle)`
Returns a [Provider](docs:developers/provider) for the provided handle.

### `craft.socialShare.getFollowersCountProviders()`
Returns a collection of all [Provider](docs:developers/provider) objects that support [Follower Counts](docs:feature-tour/follower-counts).

### `craft.socialShare.getSharesCountProviders()`
Returns a collection of all [Provider](docs:developers/provider) objects that support [Share Counts](docs:feature-tour/share-counts).

### `craft.socialShare.getShareButtonProviders()`
Returns a collection of all [Provider](docs:developers/provider) objects that support [Share Buttons](docs:feature-tour/share-buttons).

