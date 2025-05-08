# Content Element

## Creating a new content element

To add a **custom content element**, you’ll:

1. **Define a Form Type**
2. **(Optional) Register the Form Type**
3. **Implement a Renderer**
4. **Register the Renderer**
5. **Create a Twig Template**



### Form Type

Create a new form type under `src/Form/Type/ContentElements` location. Define your fields and remember to define public const `TYPE` with a unique name.\
For example, you can create a new form type called `Text`:

```php
final class TextContentElementType extends AbstractType
{
    public const TYPE = 'text';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(self::TYPE, TextType::class, [
                'label' => 'sylius_cms.ui.content_elements.type.' . self::TYPE,
            ])
        ;
    }
}
```

### (Optional) Register the Form Type

If your form type have constructor with some arguments, define constant parameter in `config/parameters.yaml` or yours any other `yaml` file:

```yaml
parameters:
    sylius_cms.content_elements.type.text: !php/const 'YourNamespace\Form\Type\ContentElements\TextContentElementType::TYPE'
```

If your form type doesn't have any constructor arguments, you can skip this step, because compiler pass will automatically define it for you.

If your form type have constructor with some arguments, you must define form type in service container under `config/services.yml` with correct tags:

```yaml
services:
    sylius_cms.form.type.content_element.text:
        class: YourNamespace\Form\Type\ContentElements\TextContentElementType
        arguments: [...]
        tags:
            - { name: 'sylius_cms.content_elements.type', key: '%sylius_cms.content_elements.type.text%' }
            - { name: 'form.type' }
```

If your form type doesn't have any constructor arguments, you can skip this step, because compiler pass will automatically register it for you.

### Implement a renderer

Create a new renderer class under `src/Renderer/ContentElement` location. Extend `Sylius\CmsPlugin\Renderer\ContentElement\AbstractContentElement` class.\
For example, you can create a new renderer called `TextContentElementRenderer`:

```php
final class TextContentElementRenderer extends AbstractContentElement
{
    public function supports(ContentConfigurationInterface $contentConfiguration): bool
    {
        return TextContentElementType::TYPE === $contentConfiguration->getType();
    }

    public function render(ContentConfigurationInterface $contentConfiguration): string
    {
        $text = $contentConfiguration->getConfiguration()['text'];

        return $this->twig->render('@SyliusCmsPlugin/shop/content_element/index.html.twig', [
            'content_element' => $this->template,
            'text' => $text,
        ]);
    }
}
```

### Register a renderer

Register your renderer with tag in service container under `config/services.yml`:

```yaml
services:
    sylius_cms.renderer.content_element.text:
        class: YourNamespace\Renderer\ContentElement\TextContentElementRenderer
        arguments:
            - '@twig'
        tags:
            - { 
                name: 'sylius_cms.renderer.content_element',
                template: '@YourNamespace/Shop/ContentElement/_text.html.twig',
                form_type: 'YourNamespace\Form\Type\ContentElements\TextContentElementType'
            }
```

Define form\_type only if your form type doesn't have constructor with additional arguments.

### Create a Twig Template

Finally, create a new template under `templates/bundles/SyliusCmsPlugin/Shop/ContentElement` location.\
For example, you can create a new template called `_text.html.twig`:

```twig
<p>{{ text }}</p>
```

