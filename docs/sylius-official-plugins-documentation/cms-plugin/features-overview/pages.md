# Pages

**Pages** are full-featured CMS-driven web pages that you can create and manage directly from the Sylius Admin Panel. They’re ideal for building landing pages, blog posts, static content (like FAQs or About pages), and more—without writing code.

## Why Use CMS Pages?

* **Rich Content Capabilities**\
  Combine text, media, product listings, and reusable blocks to create dynamic and visually rich pages.
* **SEO & Teasers**\
  Configure important SEO metadata such as:
  * Slug (URL path)
  * Meta Title
  * Keywords
  * Description\
    Add a **Teaser** (image, title, and summary) to feature your page in listings or highlight areas.
* **Flexible Layouts**\
  Embed content elements, blocks, and collections in any order using the drag-and-drop editor or a predefined content template.

{% hint style="info" %}
To display CMS pages on your storefront, make sure your project includes proper rendering support.\
See the Pages Technical Guide for integration details.
{% endhint %}

***

## How to Create a CMS Page

1. **Open the Sylius Admin Panel**
2. **Navigate to**: `CMS → Pages`
3. **Click**: `Create New Page`
4. **Fill in the Form**:
   * **Name**: Internal label used within the admin
   * **Code**: Unique identifier (e.g., `home-promo-page`)
   * **SEO Fields** _(optional)_:
     * Meta title
     * Keywords
     * Description
   * **Slug** _(optional but recommended)_:\
     Defines the URL path (e.g., `promotions/summer-sale`)
   * **Teaser** _(optional)_:\
     Add an image, title, and summary for use in listings
5. **Click Save**

📍 If you set a slug, your page will be publicly accessible at:

```
{your-store-hostname}/{locale}/pages/{slug}
```

Example:\
`https://example.com/en_US/pages/promotions/summer-sale`&#x20;

## Adding Content to a Page

1. In the page editor, scroll to the **Content** section.
2. Click **"Add Element"**
3. Choose from the following **Content Elements**:
   * Single Media
   * Multiple Media
   * Products Carousel (by or without Taxon)
   * Products Grid (by or without Taxon)
   * Pages Collection
   * Textarea
   * Heading
   * Taxons List
   * Spacer
4. Fill in each element's configuration fields (e.g., images, product filters, text).
5. Save your changes.

## Publishing Options

After saving, you can either:

* **Publish Immediately**: Make the page live right away.
* **Schedule for Later**: Choose a future date and time to automatically publish the page.

<figure><img src="../../../.gitbook/assets/cms-pages-scheduling.png" alt=""><figcaption></figcaption></figure>

***
