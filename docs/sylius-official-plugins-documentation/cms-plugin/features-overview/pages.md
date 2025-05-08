# Pages

**Pages** are full CMS-driven web pages (landing pages, informational pages, blog posts, etc.) that you can create and customize entirely from the Sylius admin.

### Why Use Pages?

* **Rich Content**\
  Combine text, images, media, product lists and more to build engaging standalone pages.
* **SEO & Teasers**\
  Configure slug, meta title, keywords, description and teaser (image + summary) for your page listings.
* **Dynamic Layouts**\
  Embed collections, blocks or content templates in any order to craft bespoke layouts.

> **Note:** Make sure you’ve wired up the CMS page rendering in your storefront. See the Pages technical guide for implementation details.

***

### Creating a Page

1. **Open Admin** → go to **CMS → Pages**
2. **Click** “Create New Page”
3. **Fill in**:
   * **Name** (internal label)
   * **Code** (unique identifier)
4. **(Optional)** Set SEO fields: Meta title, keywords, description
5. **(Optional)** Set a **Slug** field to enable access to the page once it is created.
6. **(Optional)** Add a **Teaser**: image, title & summary for listings
7. **Save** your page
8. If a slug is set, the page content will be available in the store at the following URL:

`{store-hostname}/{locale}/pages/{slug}` .

***

### Adding Content Elements

In the page editor’s **Content** section:

1. **Click** “Add Element”
2. **Select** one of available content elements:
   * Single media
   * Multiple media
   * Products carousel by Taxon
   * Products grid by Taxon
   * Pages collection
   * Textarea
   * Heading
   * Products carousel
   * Products grid
   * Taxons list
   * Spacer
3. **Fill in** each element’s fields (text, images, links…)
4. **Save.**

### Publishing

You can choose whether the new page should be available immediately after creation or scheduled for a specific date.
