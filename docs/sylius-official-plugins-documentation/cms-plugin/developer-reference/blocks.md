# Blocks

## Desing with Twig Hooks

### Configuration

Use the `sylius_cms.shop:render:block` component and pass the following props:

* `code`\
  The unique identifier of the collection you want to render.
* `template` (optional)\
  Path to a custom Twig template for rendering the block. When you provide your own template, **you** are responsible for rendering the content.
* `context` (optional)\
  Set the limitation for the product,&#x20;

### Example configuration

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.overview.accordion.details':
            dynamic_details:
                component: 'sylius_cms.shop:render:block'
                props:
                    code: 'my_block_code'
                    template: 'my_custom_template.html.twig' # optional
                    context: '@=_context.resource' #optional, see 'Context Configuration' section below
```

## Design directly in Twig Template

### Render the block

```twig
{{ sylius_cms_render_block('my_block_code') }}
```

You can render the custom template by passing the appropriate option:

```twig
{{ sylius_cms_render_block('my_block_code', 'my_custom_template.html.twig') }}
```

### Context Configuration

The context prop allows you to pass additional variables to the block template. It supports three types: **ProductInterface**, **TaxonInterface**, or **an array**.

```twig
{{ sylius_cms_render_block('homepage_intro', null, {'some_variable': 'some_value'}) }}
{{ sylius_cms_render_block('homepage_intro', null, product) }}
{{ sylius_cms_render_block('homepage_intro', null, taxon) }}
```

When you pass a **ProductInterface** or **TaxonInterface** as the context, the block will be rendered only if it is assigned to the corresponding product or taxon in the admin panel.
