# Envato

Envato follower counts report the number of followers for an Envato Market username.

The request requires a personal token from your own Envato account. Keep the token private and store it in an environment variable rather than directly in project config.

1. [Create an Envato personal token](https://build.envato.com/create-token/) for this site.
2. Add the token to the site's environment as `ENVATO_PERSONAL_TOKEN`.
3. Open **Settings → Social Share → Providers → Envato** in the Craft control panel.
4. Select `$ENVATO_PERSONAL_TOKEN` in the **Personal Token** field and save the provider.

Requests return `null` without contacting Envato when the token is missing or unresolved.
