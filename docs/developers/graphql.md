# GraphQL
Social Share supports accessing [Share](docs:feature-tour/share-counts) and [Follower](docs:feature-tour/follower-counts) counts and [Share Button](docs:feature-tour/share-buttons) and [Button](docs:feature-tour/buttons) objects via GraphQL. Be sure to read about [Craft's GraphQL support](https://craftcms.com/docs/4.x/graphql.html).

## Share Counts

### Example

:::code
```graphql GraphQL
{
    socialShare {
        shares(handle: "facebook", url: "https://www.nytimes.com")
        twitterShares: shares(handle: "twitter", url: "https://www.nytimes.com")
    }
}
```

```json JSON Response
{
    "data": {
        "socialShare": {
            "shares": "4.1M",
            "twitterShares": "5.7M"
        }
    }
}
```
:::

### The `shares` query
This query is used to query for the [Share](docs:feature-tour/share-counts) count of a provided URL and a provider handle. The `handle` and `url` arguments are required.

| Argument | Type | Description
| - | - | -
| `handle`| `String` | Narrows the query results based on the shares provider’s handle.
| `url`| `String` | Narrows the query results based on the URL to check shares for.
| `friendlyCount`| `Boolean` | Whether the returned count should be a "friendly" number.
| `enableCache`| `Boolean` | Whether to enable the cache for results.
| `cacheDuration`| `Int` | The number of seconds to cache results for.


## Follower Counts

### Example

:::code
```graphql GraphQL
{
    socialShare {
        followers(handle: "facebook", account: "nytimes")
        twitterFollowers: followers(handle: "twitter", account: "nytimes")
    }
}
```

```json JSON Response
{
    "data": {
        "socialShare": {
            "followers": "18M",
            "twitterFollowers": "54.7M"
        }
    }
}
```
:::

### The `followers` query
This query is used to query for the [Follower](docs:feature-tour/follower-counts) count of a provided account identifier and a provider handle. The `handle` and `account` arguments are required.

| Argument | Type | Description
| - | - | -
| `handle`| `String` | Narrows the query results based on the shares provider’s handle.
| `account`| `String` | Narrows the query results based on the account to check followers for.
| `friendlyCount`| `Boolean` | Whether the returned count should be a "friendly" number.
| `enableCache`| `Boolean` | Whether to enable the cache for results.
| `cacheDuration`| `Int` | The number of seconds to cache results for.


## Share Buttons

### Example

:::code
```graphql GraphQL
{
    socialShare {
        shareButtons(handle: ["facebook", "twitter"], url: "https://my-site.test") {
            url
        }

        shareButton(handle: "facebook") {
            name
            handle
            primaryColor
            icon
            url
        }
    }
}
```

```json JSON Response
{
    "data": {
        "socialShare": {
            "shareButtons": [
                {
                    "url": "https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test"
                },
                {
                    "url": "https://twitter.com/intent/tweet?url=https%3A//my-site.test"
                }
            ],
            "shareButton": {
                "name": "Facebook",
                "handle": "facebook",
                "primaryColor": "#3b5997",
                "icon": "<svg fill=\"currentColor\" viewBox=\"0 0 320 512\"><path d=\"M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z\"></path></svg>",
                "url": "https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test"
            },
        }
    }
}
```
:::


### The `shareButtons` query
This query is used to query [Share Button](docs:feature-tour/share-buttons) objects. You can also use the singular `shareButton` to fetch a single share button.

| Argument | Type | Description
| - | - | -
| `handle`| `[String]` | Narrows the query results based on the button provider’s handle.
| `url`| `String` | Provide the URL to be shared.
| `text`| `String` | Provide the text to be shared.
| `params`| `[String]` | Provide any other params to be included in the share URL.


### The `ShareButtonInterface` interface
This is the interface implemented by all share buttons.

| Field | Type | Description
| - | - | -
| `name`| `String` | The button provider’s name.
| `handle`| `String` | The button provider’s handle.
| `primaryColor`| `String` | The button provider’s primary brand color.
| `icon`| `String` | The button provider’s SVG icon.
| `url`| `String` | The button’s URL.


## Buttons

### Example

:::code
```graphql GraphQL
{
    socialShare {
        buttons(handle: ["facebook", "twitter"]) {
            primaryColor
        }

        button(handle: "facebook") {
            name
            handle
            primaryColor
            icon
        }
    }
}
```

```json JSON Response
{
    "data": {
        "socialShare": {
            "buttons": [
                {
                    "primaryColor": "#3b5997"
                },
                {
                    "primaryColor": "#1da1f2"
                }
            ],
            "button": {
                "name": "Facebook",
                "handle": "facebook",
                "primaryColor": "#3b5997",
                "icon": "<svg fill=\"currentColor\" viewBox=\"0 0 320 512\"><path d=\"M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z\"></path></svg>",
            },
        }
    }
}
```
:::


### The `buttons` query
This query is used to query [Button](docs:feature-tour/buttons) objects. You can also use the singular `button` to fetch a single button.

| Argument | Type | Description
| - | - | -
| `handle`| `[String]` | Narrows the query results based on the button provider’s handle.


### The `ButtonInterface` interface
This is the interface implemented by all buttons.

| Field | Type | Description
| - | - | -
| `name`| `String` | The button provider’s name.
| `handle`| `String` | The button provider’s handle.
| `primaryColor`| `String` | The button provider’s primary brand color.
| `icon`| `String` | The button provider’s SVG icon.

