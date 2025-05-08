# Developer Reference

## Developer Reference

Now that you’ve explored the **Feature Overview**, this section shows you _how_ to integrate and customize Sylius CMS Plugin components in your storefront:

* **Twig Hooks** — the recommended way to inject CMS features into existing templates
* **Twig Functions** — direct template calls when hooks aren’t available

To unlock the full power of the CMS plugin, pick the right entry points and inject your content where it makes sense. We’ll cover Twig Hook setups in each module below. If you’re new to Twig Hooks, start with the [Twig Hooks](../../../the-customization-guide/customizing-templates.md) guide.

Jump to the module you need:

* [Collections](collections.md)
* [Pages](pages.md)
* [Blocks](blocks.md)
* [Media](media.md)
* [Content Element](content-element.md)
* [Templates](templates.md)

## Customization

You can customize this plugin using:

* [Sylius Twig Hooks](../../../the-customization-guide/customizing-templates.md)
* [Symfony decorator pattern](https://symfony.com/doc/current/service_container/service_decoration.html)
* [Symfony form extension](https://symfony.com/doc/current/form/create_form_type_extension.html)

In order to check what services are available with this plugin, run the following command:

```bash
bin/console debug:container sylius_cms
```

**Note:**

_All forms are prefixed with 'sylius\_cms.form._'\*

If you want to check what routes are available with this plugin, use:

```bash
bin/console debug:router | grep sylius_cms
```

To check parameters available with the plugin, execute:

```bash
bin/console debug:container --parameters | grep sylius_cms
```

