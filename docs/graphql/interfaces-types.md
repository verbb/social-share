# Interfaces & Types

Social Share adds interfaces to Craft’s GraphQL schema for its buttons. Follower and share counts return plain strings, so they have no type of their own. [Queries](docs:graphql/queries) shows how to request these through the `socialShare` query. All fields are nullable.

## Interfaces

### The `SocialShareInterface` Interface

The `socialShare` query returns this interface, which holds the plugin’s queries. Select `followers`, `shares`, `shareButtons`, `shareButton`, `buttons` or `button` inside it, as described in [Queries](docs:graphql/queries#plugin-queries).

### The `ButtonInterface` Interface

A [button](docs:reference/button) for a provider, returned by the `buttons` and `button` queries. It describes the provider, and your frontend supplies the link.

```graphql
button(handle: "linkedIn") {
  name
  primaryColor
}
```

```json
{
  "button": {
    "name": "LinkedIn",
    "primaryColor": "#0077b5"
  }
}
```

::: reference
#### `name`

**Type:** `String`

The provider’s name, such as `Facebook`, used as the button label.
:::

::: reference
#### `handle`

**Type:** `String`

The provider’s handle, such as `facebook`.
:::

::: reference
#### `primaryColor`

**Type:** `String`

The provider’s brand colour as a hex value, or `null` when it has none.
:::

::: reference
#### `icon`

**Type:** `String`

The provider’s icon as SVG markup, or `null` when it has none.
:::

### The `ShareButtonInterface` Interface

A [share button](docs:reference/share-button) for a provider, returned by the `shareButtons` and `shareButton` queries. It has the same fields as `ButtonInterface`, plus the share link.

::: reference
#### `name`

**Type:** `String`

The provider’s name, such as `Facebook`, used as the button label.
:::

::: reference
#### `handle`

**Type:** `String`

The provider’s handle, such as `facebook`.
:::

::: reference
#### `primaryColor`

**Type:** `String`

The provider’s brand colour as a hex value, or `null` when it has none.
:::

::: reference
#### `icon`

**Type:** `String`

The provider’s icon as SVG markup, or `null` when it has none.
:::

::: reference
#### `url`

**Type:** `String`

The provider’s share link for the URL passed to the query, or `null` when the provider can’t share. Always pass `url`, because without it the link falls back to the current request’s URL, which for a GraphQL request is your API endpoint.
:::
