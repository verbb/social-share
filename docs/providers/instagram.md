# Instagram

Instagram follower counts come from an Instagram professional account connected to a Facebook Page. The Meta account used to connect must be able to manage that Page.

1. Create or choose a Meta developer app with Facebook Login and access to the Instagram API.
2. Confirm that the Instagram professional account is linked to a Facebook Page managed by the account that will complete the connection.
3. Open **Settings → Social Share → Providers → Instagram** in the Craft control panel.
4. Add the displayed **Redirect URI** to the app's valid OAuth redirect URIs in Meta.
5. Store the Meta app's **Client ID** and **Client Secret** in environment variables, then select those variables in the corresponding Social Share fields.
6. Save the provider and select **Connect**.
7. Complete the authorisation using the Meta account that manages the linked Facebook Page.

Social Share requests the connected account's Pages and uses the first Page with a linked Instagram business account. Calls to `craft.socialShare.getFollowers('instagram', account)` return that Instagram account's follower count. The required `account` argument is retained for compatibility and does not select another account.
