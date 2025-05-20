# UPGRADE FROM `2.0` TO `2.1`

1. The `sylius_admin_customer_orders_statistics` route has been deprecated.

1. The minimum version of Symfony 7 packages has been bumped from Symfony `^7.1` to `^7.2`

1. The `sylius_admin.dashboard.index.content.latest_statistics.new_customers` hook has been deprecated and disabled. 
   It has been replaced by the `sylius_admin.dashboard.index.content.latest_statistics.pending_actions`.
