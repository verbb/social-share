# Mailchimp

Mailchimp follower counts report the number of members in an audience. Pass that audience's list ID as the account argument when calling `craft.socialShare.getFollowers()`.

The request requires a Mailchimp Marketing API key from your own account. Mailchimp API keys grant broad account access, so give this integration its own key and store it in an environment variable rather than directly in project config.

1. Follow Mailchimp's [API key instructions](https://mailchimp.com/help/about-api-keys/) to create a key for this site.
2. Add the key to the site's environment as `MAILCHIMP_API_KEY`.
3. Open **Settings → Social Share → Providers → Mailchimp** in the Craft control panel.
4. Select `$MAILCHIMP_API_KEY` in the **API Key** field and save the provider.

Requests return `null` without contacting Mailchimp when the key is missing, unresolved, or doesn't include a valid Mailchimp data centre suffix.
