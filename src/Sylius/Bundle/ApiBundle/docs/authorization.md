# Sylius API - Authorization

The Sylius API now relies on HTTP Basic authentication handled directly by Symfony's security component. There are no
JWT tokens involved – every request that targets a protected endpoint has to contain an `Authorization` header with a
`Basic` value built from a valid e‑mail/password pair.

> Admin and shop APIs share the same mechanism, but they still use separate user providers. Make sure that the
> credentials you use belong to a user that has the required roles (for example `ROLE_API_ACCESS`).

1. Encode your credentials

    HTTP Basic uses the pattern `Authorization: Basic base64(email:password)`. For example, for the default API admin
    shipped with the fixtures (`api@example.com` / `sylius`) the header value can be generated with:

    ```bash
    echo -n 'api@example.com:sylius' | base64
    ```

    The resulting string (for example `YXBpQGV4YW1wbGUuY29tOnN5bGl1cw==`) should be used together with the `Basic`
    prefix: `Authorization: Basic YXBpQGV4YW1wbGUuY29tOnN5bGl1cw==`.

2. Call any API endpoint while providing the header

    ```bash
    curl -H 'Authorization: Basic YXBpQGV4YW1wbGUuY29tOnN5bGl1cw==' \
         -H 'Accept: application/ld+json' \
         http://127.0.0.1:8000/api/v2/admin/administrators
    ```

    Replace the credentials with shop users if you want to call `/api/v2/shop/...` endpoints. The default shop account
    from fixtures is `shop@example.com` / `sylius`.

3. Use the API Platform Swagger UI (`/api/v2/docs`)

    The documentation exposes a **basicAuth** security scheme. Click the **Authorize** button, enter the same
    `email:password` combination, and Swagger will automatically attach the appropriate `Authorization: Basic ...`
    header to every subsequent request.
