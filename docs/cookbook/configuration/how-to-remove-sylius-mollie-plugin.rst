How to remove Sylius Mollie Plugin in Sylius 1.14
=================================================

The SyliusMolliePlugin is included by default in Sylius Standard 1.14.

If you wish to remove it, follow these steps:

Removing dependency
-------------------

Start by running the following command:

.. code-block:: bash

    composer remove sylius/mollie-plugin
    yarn remove bazinga-translator intl-messageformat lodash.get shepherd.js

This one will completely remove the dependencies along with the configuration files and routes.

Update entities:
----------------

To do so, update your rector config:

.. code-block:: php

    // /rector.php

    <?php

    declare(strict_types=1);

    use Rector\Config\RectorConfig;
    use Sylius\SyliusRector\Set\SyliusMollie;

    return static function (RectorConfig $rectorConfig): void {
        $rectorConfig->paths([
            __DIR__ . '/src',
        ]);

        // Rule set dedicated to the Sylius Mollie Plugin
        $rectorConfig->sets([
            SyliusMollie::REMOVE_MOLLIE_PLUGIN_FROM_SYLIUS_114,
        ]);

        // Remove unused imports if necessary
        $rectorConfig->importNames();
        $rectorConfig->removeUnusedImports();
    };

Then run:

.. code-block:: bash

    vendor/bin/rector process src

.. warning::

    If you don't want to use rector you can also do it manually, by reverting the changes made by `This PR <https://github.com/Sylius/Sylius-Standard/pull/1117/files>`_.

Remove overwritten templates:
-----------------------------

.. code-block:: bash

    rm -rf templates/bundles/SyliusAdminBundle/Order/Show/Summary/_totalsPromotions.html.twig
    rm -rf templates/bundles/SyliusAdminBundle/PaymentMethod/_form.html.twig
    rm -rf templates/bundles/SyliusShopBundle/Checkout/SelectPayment/_payment.html.twig
    rm -rf templates/bundles/SyliusShopBundle/Common/Order/Table/_totals.html.twig
    rm -rf templates/bundles/SyliusShopBundle/Common/Order/_table.html.twig
    rm -rf templates/bundles/SyliusShopBundle/Order/_summary.html.twig

Now you are ready to go!
