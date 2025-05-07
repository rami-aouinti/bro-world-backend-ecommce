# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

- `api-platform/jsonld: ^4.1.1`

  API Platform introduced changes in version 4.1.1 that modify API responses, potentially breaking compatibility with our current implementation.  
  To ensure stable behavior, we have added this conflict until we can verify and adapt to the changes.

- `behat/gherkin:^4.13.0`:

  This version moved files to flatten paths into a PSR-4 structure, which lead to a fatal error:
  `PHP Fatal error:  Uncaught Error: Failed opening required '/home/runner/work/Sylius/Sylius/vendor/behat/gherkin/src/../../../i18n.php' (include_path='.:/usr/share/php') in /home/runner/work/Sylius/Sylius/vendor/behat/gherkin/src/Keywords/CachedArrayKeywords.php:34`
