---
paths:
  - routes/api.php
---

# Routes

## Sanctum-protected API routes
Prime Capital API is a token-authenticated REST API. Protect authenticated endpoints with the `auth:sanctum` middleware. Issue and validate bearer tokens via Laravel Sanctum.

## Route groups
- Public: `GET /health/database`, `POST /register`, `POST /login` (throttle:auth)
- Authenticated: logout, me, portfolios, transactions, admin (throttle:api)
- Nested transactions: `Route::apiResource('portfolios.transactions', ...)->shallow()` — index/store under portfolio, show/update/destroy on `/transactions/{id}`

## Admin routes
Admin-only routes live under `/admin/*` and are protected by `UserPolicy::viewAny` in the controller.
