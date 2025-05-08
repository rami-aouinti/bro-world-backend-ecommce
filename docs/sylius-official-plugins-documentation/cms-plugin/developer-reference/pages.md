# Pages

## Desing with Twig Hooks

Use the `sylius_cms.shop:render:page_link` component and pass the following props:

* `code` (string, required)\
  The unique identifier of the collection you want to render.

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.overview.accordion.details':
            dynamic_details:
                component: 'sylius_cms.shop:render:page_link'
                props:
                    code: 'my_page_code'
                    
```

You can use the template option, but you’ll be fully responsible for rendering the page:

```yaml
# ...
code: 'my_page_code'
template: 'my_custom_page.html.twig'
```

## Design directly in Twig Template

### Render the page link

```twig
{{ sylius_cms_render_page_link('my_page_code') }}
```

You can override the name by passing the appropriate option:

```twig
{{ sylius_cms_render_page_link('my_page_code', {name: 'Custom URL visible name'}) }}
```

### Render only the bare URL

```twig
{{ sylius_cms_get_page_url('my_page_code') }}
```

### Render entire page embedded&#x20;

You can render the entire page at a low level by using the generic route and passing the page slug as a parameter:

```twig
{{ render(path('sylius_cms_shop_page_show', {'slug' : 'some-page-slug'}))}}
```
