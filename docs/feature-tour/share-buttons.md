# Share Buttons
You can provide your users a means to quickly share a page to social media with the click of a button, through share buttons. For example, on your blog posts, you might like to provide buttons for users to share the page on Twitter or Facebook.

The following providers support share buttons:

- Blogger
- Bluesky
- Buffer
- Diaspora
- Digg
- Douban
- Email
- Evernote
- Facebook
- Flipboard
- Gab
- Gettr
- Gmail
- HackerNews
- Instapaper
- Iorbix
- Kakao
- Kik
- KindleIt
- Kooapp
- Line
- LinkedIn
- LiveJournal
- Mail.ru
- Mastodon
- Meneame
- Messenger
- MeWe
- Mix
- Odnoklassniki
- Outlook.com
- Parler
- Pinterest
- Pocket
- PrintProvider
- Qzone
- Reddit
- Refind
- Renren
- Skype
- Sms
- Surfingbird
- Telegram
- TencentQQ
- Threema
- Trello
- Tumblr
- Viber
- Vkontakte
- WhatsApp
- Wordpress
- X (Twitter)
- Xing
- Yammer
- Yummly

:::tip
Looking for buttons to link off to a URL? Check out [Buttons](docs:feature-tour/buttons).
:::

## Getting Share Button Providers
You can fetch all providers that support share buttons. This will return a collection of [Provider](docs:developers/provider) objects.

```twig
{% for provider in craft.socialShare.getShareButtonProviders() %}
    {{ provider.name }}
{% endfor %}
```

## Rendering a Share Button
To render a share button, all you'll need is to pick which provider you want to render.

```twig
{{ craft.socialShare.renderShareButton('facebook') }}

{# Which renders... #}
<a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test/my-url" target="_blank" rel="nofollow noopener noreferrer" aria-label="Facebook" data-social-share-popup data-url="https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test/my-url" style="--brand-color: #3b5997;">
    <span>
        <span>
            <svg ...>
        </span>
        <span>
            <span>Facebook</span>
        </span>
    </span>
</a>
```

This produces a Facebook icon that opens the provider’s share prompt in a popup window. The popup behaviour comes from Social Share’s bundled external script, so it works with a strict Content Security Policy that allows the site’s own assets. The link retains the real provider URL and opens in a new tab when JavaScript is unavailable or a popup cannot be created.

When the `url` option is omitted, Social Share uses the matched element's canonical URL, or the current site's configured base URL and request path for routes that are not elements. Request query parameters and Craft preview tokens are not included. Sites without an absolute configured base URL should always pass `url` explicitly.

### Render Options
You can also pass in options to control rendering. Read further on [Rendering Buttons](docs:template-guides/rendering-buttons).

For Share Buttons, you can also pass in additional parameters to add to the share URL for the provider. For example, most providers allow you to add text alongside the URL to be shared. Pinterest and some other provideers also allows you to set an image to be posted alongside the link and text.

So to review, the URL to share something on Facebook would be:

```twig
https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test/my-url
```

You can add additional query parameters to that URL with the following:

```twig
{{ craft.socialShare.renderShareButton('facebook', {
    url: entry.url,
    text: entry.title,

    params: {    
        image: entry.featuredImage.one.url,
    },
}) }}
```

Here, we're assuming we have an `entry` variable present, and are sending additional content to the provider. This link would now look like:

```twig
https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test/my-url&text=This+is+amazing!&image=https://...
```

## Manually Rendering a Share Button
Now, if you're a little particular on how buttons are rendered, you can take total control over the rendering yourself, and just use Social Share's providers to get the data you require to generate these buttons on your own.

Here's an example of us doing just that!

```twig
{% set button = craft.socialShare.getShareButton('facebook') %}

{{ tag('a', {
    html: button.icon,
    href: button.providerUrl,
    target: '_blank',
    rel: 'nofollow noopener noreferrer',
    class: ['social-btn', button.handle],
    title: button.name,
    style: {
        color: button.primaryColor,
    },
}) }}

{# Would render... #}
<a href="..." target="_blank" rel="nofollow noopener noreferrer" class="social-btn facebook" title="Facebook" style="color: #3b5997;"><svg ...</a>
```

We're using the `tag()` Twig function because we think it looks a lot cleaner, but you could write regular Twig or HTML if you prefer. This manual example uses a normal new-tab link. If you add custom popup behaviour, keep the real provider URL in `href` and implement the click handler in an external script rather than an inline `onclick` attribute.

## Theming
You can also get Social Share to render the button in an opinionated, hands-off way. Read further on [Rendering Buttons](docs:template-guides/rendering-buttons).
