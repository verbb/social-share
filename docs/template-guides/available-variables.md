# Available Variables
The following are common methods you will want to call in your front end templates:

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

### `craft.socialShare.getFollowers(handle, account, options)`
Returns a the number of followers for a given provider and identifier (username, ID, etc).

### `craft.socialShare.getShares(handle, url, options)`
Returns a the number of shares for a given provider and url.

### `craft.socialShare.getShareButton(handle, options)`
Returns a [Share Button](docs:developers/share-button) for a given provider.

### `craft.socialShare.renderShareButton(handle, options)`
Returns a rendered [Share Button](docs:developers/share-button) for a given provider.

### `craft.socialShare.getButton(handle, options)`
Returns a [Button](docs:developers/button) for a given provider.

### `craft.socialShare.renderButton(handle, options)`
Returns a rendered [Button](docs:developers/button) for a given provider.
