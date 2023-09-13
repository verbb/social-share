# Share Buttons
You can provide your users a means to quickly share a page to social media with the click of a button, through share buttons. For example, on your blog posts, you might like to provide buttons for users to share the page on Twitter or Facebook.

The following providers support share buttons:

- Blogger
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
<a href="javascript:void();" aria-label="Facebook" onclick="window.open(this.dataset.url, &quot;ss_share_dialog&quot;, &quot;width=626,height=436&quot;);" data-url="https://www.facebook.com/sharer/sharer.php?u=https%3A//my-site.test/my-url" style="--brand-color: #3b5997;">
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

This will produce a Facebook icon, which when clicked, will open a new popup window with a share prompt dialog for the user to continue.

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

{{ tag('a', button.icon, {
    href: 'javascript:void();',
    onclick: 'window.open(this.dataset.url, "ss_share_dialog", "width=626,height=436");',
    class: ['social-btn', button.handle],
    title: button.name,
    data: {
        url: button.url,
    },
    style: {
        color: button.primaryColor,
    },
}) }}

{# Would render... #}
<a href="..." onclick="..." data-url="..." class="social-btn facebook" title="Facebook" style="color: #3b5997;"><svg ...</a>
```

We're using the `tag()` Twig function because we think it looks a lot cleaner, but you could totally write regular Twig/HTML if you prefer. Also note the use of our `href`, `data-url` and `onclick` attributes, to handle opening in a new popup window.

## Theming
You can also get Social Share to render the button in an opinionated, hands-off way. Read further on [Rendering Buttons](docs:template-guides/rendering-buttons).

