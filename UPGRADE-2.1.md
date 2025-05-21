# UPGRADE FROM `2.0` TO `2.1`

1. The `sylius_admin_customer_orders_statistics` route has been deprecated.

1. The minimum version of Symfony 7 packages has been bumped from Symfony `^7.1` to `^7.2`

1. The `sylius_admin.dashboard.index.content.latest_statistics.new_customers` hook has been deprecated and disabled. 
   It has been replaced by the `sylius_admin.dashboard.index.content.latest_statistics.pending_actions`.

### Twig hooks
1. The `history`, `cancel` and `resend_confirmation_email` hookables from `'sylius_admin.order.show.content.header.title_block.actions'` hook have been deprecated and disabled. Now these templates are located in `'sylius_admin.order.show.content.header.title_block.actions.list'` hook.
