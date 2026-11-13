# Queries

Social Share lets a headless frontend fetch follower counts, share counts and button details through Craft’s GraphQL API. Every query sits inside a single `socialShare` query, which groups the plugin’s content together.

## Set Up the Schema

In **GraphQL → Schemas**, edit the schema your frontend uses and enable **Query Social Share** under **Social Share**. Without it, the `socialShare` query isn’t part of the schema. Use **GraphQL → GraphiQL** with that schema to try the examples below, and see [Craft’s GraphQL documentation](https://craftcms.com/docs/5.x/development/graphql.html) for endpoints and tokens.

Follower and share counts need their provider configured first, as described in its provider guide, such as [Facebook](docs:providers/facebook). GraphQL count queries always use the cache settings and request limits configured for the site.

## Plugin Queries

### The `socialShare` Query

Returns the Social Share content the schema can read. It takes no arguments, and you select `followers`, `shares`, `shareButtons`, `shareButton`, `buttons` or `button` inside it.

```graphql
{
  socialShare {
    followers(handle: "facebook", account: "nytimes")
    shares(handle: "facebook", url: "https://www.nytimes.com")
  }
}
```

```json
{
  "data": {
    "socialShare": {
      "followers": "18M",
      "shares": "4.1M"
    }
  }
}
```

### The `followers` Query

Returns the [follower count](docs:feature-tour/follower-counts) for an account on a provider, as a string. It returns `null` when the provider can’t fetch the count, and an account identifier longer than 255 bytes returns `null` without a request.

| Argument | Type | Description |
| --- | --- | --- |
| `handle` | `String` | The provider’s handle, such as `facebook`. Required. |
| `account` | `String` | The account identifier to count followers for. Required. |
| `friendlyCount` | `Boolean` | Whether to return a shortened number, such as `18M`. Defaults to the `friendlyCount` setting. |
| `enableCache` | `Boolean` | Accepted but ignored. The site’s Social Share settings control caching. |
| `cacheDuration` | `Int` | Accepted but ignored. The site’s Social Share settings control the cache duration. |

Use aliases to request several providers in one query:

```graphql
{
  socialShare {
    facebook: followers(handle: "facebook", account: "nytimes")
    twitter: followers(handle: "twitter", account: "nytimes", friendlyCount: false)
  }
}
```

```json
{
  "data": {
    "socialShare": {
      "facebook": "18M",
      "twitter": "54700000"
    }
  }
}
```

### The `shares` Query

Returns the [share count](docs:feature-tour/share-counts) for a URL on a provider, as a string. It returns `null` when the provider can’t fetch the count, and a URL longer than 2048 bytes returns `null` without a request.

| Argument | Type | Description |
| --- | --- | --- |
| `handle` | `String` | The provider’s handle, such as `facebook`. Required. |
| `url` | `String` | The URL to count shares for. Required. |
| `friendlyCount` | `Boolean` | Whether to return a shortened number, such as `4.1M`. Defaults to the `friendlyCount` setting. |
| `enableCache` | `Boolean` | Accepted but ignored. The site’s Social Share settings control caching. |
| `cacheDuration` | `Int` | Accepted but ignored. The site’s Social Share settings control the cache duration. |

```graphql
query Shares($url: String) {
  socialShare {
    shares(handle: "facebook", url: $url)
  }
}
```

```json
{
  "url": "https://www.nytimes.com"
}
```

```json
{
  "data": {
    "socialShare": {
      "shares": "4.1M"
    }
  }
}
```

### The `shareButtons` Query

Returns a list of [`ShareButtonInterface`](docs:graphql/interfaces-types#the-sharebuttoninterface-interface) objects, one for each provider handle, so your frontend can build its own [share buttons](docs:feature-tour/share-buttons). Each button’s `url` is the provider’s share link for the URL you pass.

| Argument | Type | Description |
| --- | --- | --- |
| `handle` | `[String]` | The handles of the providers to return buttons for. Required. |
| `url` | `String` | The URL to share. Always pass it, because without it the share link uses the current request’s URL, which is your GraphQL endpoint. |
| `text` | `String` | The text to share with the URL, for providers that accept it. |
| `params` | `[String]` | Extra query parameters to include in each share URL. |

```graphql
{
  socialShare {
    shareButtons(handle: ["facebook", "linkedIn"], url: "https://my-site.test/news/launch") {
      name
      primaryColor
      url
    }
  }
}
```

```json
{
  "data": {
    "socialShare": {
      "shareButtons": [
        {
          "name": "Facebook",
          "primaryColor": "#3b5997",
          "url": "https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test/news/launch"
        },
        {
          "name": "LinkedIn",
          "primaryColor": "#0077b5",
          "url": "https://www.linkedin.com/shareArticle?url=https%3A//my-site.test/news/launch&mini=1"
        }
      ]
    }
  }
}
```

### The `shareButton` Query

Returns a single [`ShareButtonInterface`](docs:graphql/interfaces-types#the-sharebuttoninterface-interface) object. It takes the same arguments as `shareButtons`, and uses the first handle you pass.

```graphql
{
  socialShare {
    shareButton(handle: "facebook", url: "https://my-site.test") {
      name
      handle
      primaryColor
      icon
      url
    }
  }
}
```

```json
{
  "data": {
    "socialShare": {
      "shareButton": {
        "name": "Facebook",
        "handle": "facebook",
        "primaryColor": "#3b5997",
        "icon": "<svg fill=\"currentColor\" viewBox=\"0 0 320 512\"><path d=\"M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z\"></path></svg>",
        "url": "https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test"
      }
    }
  }
}
```

### The `buttons` Query

Returns a list of [`ButtonInterface`](docs:graphql/interfaces-types#the-buttoninterface-interface) objects, one for each provider handle. Use these for [buttons](docs:feature-tour/buttons) that link to your own profiles, where you supply the link yourself.

| Argument | Type | Description |
| --- | --- | --- |
| `handle` | `[String]` | The handles of the providers to return buttons for. Required. |

```graphql
{
  socialShare {
    buttons(handle: ["facebook", "linkedIn"]) {
      name
      primaryColor
    }
  }
}
```

```json
{
  "data": {
    "socialShare": {
      "buttons": [
        { "name": "Facebook", "primaryColor": "#3b5997" },
        { "name": "LinkedIn", "primaryColor": "#0077b5" }
      ]
    }
  }
}
```

### The `button` Query

Returns a single [`ButtonInterface`](docs:graphql/interfaces-types#the-buttoninterface-interface) object. It takes the same `handle` argument as `buttons`, and uses the first handle you pass.

```graphql
{
  socialShare {
    button(handle: "facebook") {
      name
      handle
      primaryColor
      icon
    }
  }
}
```

```json
{
  "data": {
    "socialShare": {
      "button": {
        "name": "Facebook",
        "handle": "facebook",
        "primaryColor": "#3b5997",
        "icon": "<svg fill=\"currentColor\" viewBox=\"0 0 320 512\"><path d=\"M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z\"></path></svg>"
      }
    }
  }
}
```
