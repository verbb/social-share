# Dribbble

Dribbble follower counts come from the Dribbble account connected to Social Share. Create a Dribbble application for the site, then use its credentials to authorise that account.

1. Open Dribbble's [application registration page](https://dribbble.com/account/applications/new) and create an application.
2. Open **Settings → Social Share → Providers → Dribbble** in the Craft control panel.
3. Copy the displayed **Redirect URI** into the application's **Callback URL** field in Dribbble.
4. Store the Dribbble **Client ID** and **Client Secret** in environment variables, then select those variables in the corresponding Social Share fields.
5. Save the provider and select **Connect**.
6. Complete the authorisation using the Dribbble account whose follower count the site should display.

Calls to `craft.socialShare.getFollowers('dribbble', account)` return the connected account's follower count. The required `account` argument is retained for compatibility and does not select a different Dribbble account.
