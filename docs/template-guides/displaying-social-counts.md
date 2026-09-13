# Displaying Social Counts

Request the count for the intended account or URL. The account identifier depends on the provider; use the relevant provider setup rather than assuming every service accepts the same username format.

## Calls Used in This Task

### `craft.socialShare.getFollowers(handle, account, options)`
Returns a the number of followers for a given provider and identifier (username, ID, etc).

### `craft.socialShare.getShares(handle, url, options)`
Returns a the number of shares for a given provider and url.

