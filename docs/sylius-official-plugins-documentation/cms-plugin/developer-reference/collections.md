# Collections

## Design with Twig Hooks

Use the `sylius_cms.shop:render:collection` component and pass the following props:

* `code` (string, required)\
  The unique identifier of the collection you want to render.
* `count_to_render` (int, optional)\
  Limits the number of items displayed:
  * If `count_to_render` ≤ 0, **all** items in the collection will be rendered.
  * If `count_to_render` exceeds the total number of items, **all** items will be rendered.
  * Otherwise, only the specified number of items will be rendered.
*   `template` (string, optional)\
    Path to a custom Twig template for rendering the collection. When you provide your own template, **you** are responsible for rendering the collection items inside it. For example, your template might look like this:

    ```twig
    {# templates/collection/custom.html.twig #}
    <div>
      {{ content|raw }}
    </div>

    ```

### Example:

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.overview.accordion.details':
            dynamic_details:
                component: 'sylius_cms.shop:render:collection'
                props:
                    code: 'some_collection_code'
                    count_to_render: 1
                    template: 'collection/custom.html.twig'

```

## Design directly in Twig Template

Just call the predefined Twig function:

```twig
{{ sylius_cms_render_collection('some_collection_code') }}
```

You can also customize how many items are rendered and use your own template:

```yaml
{{ sylius_cms_render_collection(
    'some_collection_code', 
    3,
    'collection/custom.html.twig'
) }}
```

## Miscellaneous

By default, collection items are sorted by their object ID. To change this behavior, you can use a decorator strategy.

Learn more about it [here](https://symfony.com/doc/current/service_container/service_decoration.html).

