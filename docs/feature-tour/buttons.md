# Buttons
Most website's these days have links to the client's social media accounts in the header, footer or contact pages. Social Share can help with rendering these, saving you having to lug around a collection of icons for social media platforms from project to project, and save time with development. You can also use our theme options to really speed along development!

All 90+ providers support regular buttons.

:::tip
Looking for buttons that share the current URL to social media? Check out [Share Buttons](docs:feature-tour/share-buttons).
:::

## Rendering a Button
To render a button, all you'll need is to pick which provider you want to render, and provide a URL for the destination.

```twig
{{ craft.socialShare.renderButton('facebook', {
    url: 'https://www.facebook.com/craftcms',
}) }}
```

This will produce a Facebook icon, which when clicked, will open a new window to `https://www.facebook.com/craftcms`.

### Render Options
You can also pass in options to control rendering. Read further on [Rendering Buttons](docs:template-guides/rendering-buttons).

## Manually Rendering a Button
Now, if you're a little particular on how buttons are rendered, you can take total control over the rendering yourself, and just use Social Share's providers to get the data you require to generate these buttons on your own.

Here's an example of us doing just that!

```twig
{% set button = craft.socialShare.getButton('facebook') %}

{{ tag('a', button.icon, {
    href: '...',
    class: ['social-btn', button.handle],
    title: button.name,
    style: {
        color: button.primaryColor,
    },
}) }}

{# Would render... #}
<a href="..." class="social-btn facebook" title="Facebook" style="color: #3b5997;"><svg ...</a>
```

We're using the `tag()` Twig function because we think it looks a lot cleaner, but you could totally write regular Twig/HTML if you prefer.

## Theming
You can also get Social Share to render the button in an opinionated, hands-off way. Read further on [Rendering Buttons](docs:template-guides/rendering-buttons).

