# Media

## Desing with Twig Hooks

Use the `sylius_cms.shop:render:media` component and pass the following props:

* `code`\
  The unique identifier of the collection you want to render.
* `template` (optional)\
  Path to a custom Twig template for rendering the media. When you provide your own template, **you** are responsible for rendering the content.

### Example configuration:

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.overview.accordion.details':
            dynamic_details:
                component: 'sylius_cms.shop:render:media'
                props:
                    code: 'my_media_code'
                    template: 'my_custom_template.html.twig' # optional
```

## Design directly in Twig Template

### Render the media

```twig
{{ sylius_cms_render_media('my_media_code') }}
```

You can render the custom template by passing the appropriate option:

```twig
{{ sylius_cms_render_media('my_media_code', 'my_custom_template.html.twig') }}
```

### Render the media directly by calling the route

```twig
{{ render(path('sylius_cms_shop_media_render', {'code' : 'my_media_code' })) }}
```

## Media provider

You can add your own media provider by adding a service with a tag named `sylius_cms.media_provider`:

```yaml
app.media_provider.audio:
    class: Sylius\CmsPlugin\MediaProvider\GenericProvider
    arguments:
        - "@sylius_cms.media_uploader"
        - "@templating.engine.twig"
        - "@@SyliusCmsPlugin/shop/media/show/audio.html.twig"
        - "media/audio"
    tags:
        - { name: sylius_cms.media_provider, type: audio, label: sylius_cms.ui.audio_provider }

```
