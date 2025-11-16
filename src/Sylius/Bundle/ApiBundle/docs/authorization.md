# Sylius API - Authorization

The Sylius API relies on HTTP Basic authentication handled directly by Symfony's security component. Any request that targets a
protected endpoint must contain an `Authorization` header with a `Basic` value generated from a valid e-mail/password pair.

## Example endpoints

The same header can be used across the different resources you need to query. Typical shop endpoints include:

- `GET http://127.0.0.1:8000/api/v2/shop/customers`
- `GET http://127.0.0.1:8000/api/v2/shop/products`
- `GET http://127.0.0.1:8000/api/v2/shop/orders`

## Example request

```bash
curl -H 'Authorization: Basic <BASE64_CREDENTIALS>' \
     -H 'Accept: application/ld+json' \
     http://127.0.0.1:8000/api/v2/shop/orders
```
