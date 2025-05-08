# Collections

The **Collections** feature lets administrators group and manage related content—blocks, CMS pages, and media—into named containers. This structure makes it easy to build consistent, reusable layouts across your store.

## Why Use Collections?

* **Organize content**\
  Group blog posts, product galleries, promotional banners, or any other elements under a single collection.
* **Maintain consistency**\
  Apply the same set of blocks, pages, or media to multiple CMS pages at once.
* **Increase flexibility**\
  Rearrange, add, or remove items within a collection without touching individual pages.

## Key Concepts

* **Container**\
  A collection acts as a wrapper: you attach a bundle of blocks, pages, or media to it.
* **Presentation**\
  Collections determine how and where content appears on your storefront.
* **Customization**\
  Use collections to tailor page layouts for different marketing campaigns or user segments.

## Creating a Collection

1. **Open the Sylius admin panel**
2. **Go to** **CMS → Collections**
3. **Click** **“Create”**
4.  **Fill in** the form fields:

    * **Code** - a unique identifier
    * **Name** - the display label
    * **Type -** select whether the collection will contain pages, blocks, or media
    * **Content field** – appears below as a dynamic Autocomplete input (labelled Pages, Blocks, or Media, depending on the selected Type); you can select multiple elements to include in the collection.


5. Submit the form.
6.  The collection content will be available in the store at the following URL

    `{store-hostname}/{locale}/collections/{collection-code}/pages`

