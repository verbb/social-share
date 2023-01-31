# Rendering Buttons
When rendering either [Share Buttons](docs:feature-tour/share-buttons) or [Buttons](docs:feature-tour/buttons), they both follow the same implementation and customisation options.

When rendering a button, you have the choice to either fetch just the data for a button and render it yourself, or get Social Share to render it for you. 

We'll be focusing here on the "rendering it for you" approach, and also cover Social Share's theming for an even more hands-off approach.

:::tip
Wherever we use `craft.socialShare.getButton()` or `craft.socialShare.renderButton()` know that these are interchangeable with `getShareButton()` and `renderShareButton()`.
:::

### Render Options
By default, no styling will be applied to buttons when rendering, so it'll look 🤮. You can pass in options for adding attributes to the rendered button. The HTML by default for an icon, will look something like:

```twig
<a href="..." rel="nofollow noopener noreferrer" aria-label="Facebook" target="_blank" style="--brand-color: #3b5997;">
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

And through render options, we can apply our own attributes on each HTML element. Let's add some `class` attributes to these elements, so we can style them.

```twig
{{ craft.socialShare.renderButton('facebook', {
    url: '...',

    buttonAttributes: {
        class: 'share-btn',
    },
    contentAttributes: {
        class: 'share-btn-wrapper',
    },
    iconWrapperAttributes: {
        class: 'share-btn-icon',
    },
    labelWrapperAttributes: {
        class: 'share-btn-label-wrapper',
    },
    labelAttributes: {
        class: 'share-btn-label',
    },
}) }}

{# Would render... #}

<a class="share-btn" href="..." rel="nofollow noopener noreferrer" aria-label="Facebook" target="_blank" style="--brand-color: #3b5997;">
    <span class="share-btn-wrapper">
        <span class="share-btn-icon">
            <svg ...>
        </span>
        <span class="share-btn-label-wrapper">
            <span class="share-btn-label">Facebook</span>
        </span>
    </span>
</a>
```

Of course, you can add more than just classes, any HTML attribute goes!

## Theming
When rendering a button, you'll need to use the render options to add classes to HTML elements to style it. Or, you'll need to use manually render your own button.

But - another way is to use our theming option to render the buttons in an opinionated style. This might not suit every project, but it certainly speeds up your development time when you just want to quickly output social icons.

To enable theming, just pass in `theme: true`.

```twig
{{ craft.socialShare.renderButton('facebook', {
    url: '...',
    theme: true,
}) }}
```

This will generate a minimally-styled button, and you're free to call it a day! But there's more options available to you:

```twig
{{ craft.socialShare.renderButton('facebook', {
    url: '...',
    theme: {
        showIcon: true,
        showLabel: true,
        shape: 'rounded',
    },
}) }}
```

This will produce an icon including `Facebook` as the label, and rounded corners.

All available options are:

Attribute | Options | Default
--- | ---
`showIcon` | `true` or `false` | `true`
`showLabel` | `true` or `false` | `false`
`shape` | `rectangle`, `rounded` or `circle` | `rectangle`
`iconColor` | `brand`, `black` or `white` | `white`
`bgColor` | `brand`, `black` or `white` | `brand`
`iconHoverColor` | `brand`, `black` or `white` | `white`
`bgHoverColor` | `brand`, `black` or `white` | `brand`

### Override Theming
You can even take theming further by combining regular rendering with theme settings. Because we use CSS variables, this makes configuring aspects about the icon much easier.

For example, let's say we're happy with the `rounded` style of icon with just the icon. But the default border radius is a bit small. And we also want the size of the button smaller.

```twig
{{ craft.socialShare.renderButton('facebook', {
    url: '...',
    buttonAttributes: {
        style: {
            '--size': '25px',
            '--border-radius': '10px',
        },
    },
    theme: {
        showIcon: true,
        shape: 'rounded',
    },
}) }}
```

Here, we're modifying the CSS variables for the theme to produce a small "squirqle" shape (not quite square, not quite circle).

We could also produce a comically large button, with the actual provider logo small.

```twig
{{ craft.socialShare.renderButton('facebook', {
    url: '...',
    buttonAttributes: {
        style: {
            '--size': '100px',
            '--icon-size': '20px',
        },
    },
    theme: true,
}) }}
```

The size of the button will be `100px` but the "F" logo will be only `20px`.

### CSS Variables
You can add the following attributes to the `buttonAttributes` element, which serves as the `:root` element for the rest of the button.

Attribute | Default
--- | ---
`--transition` | `all 0.2s ease`
`--icon-size` | `50%`
`--size` | `40px`
`--gap` | `5px`
`--spacing` | `10px`
`--font-size` | `14px`
`--border-radius` | `4px`
`--border-radius-large` | `20px`
