---
paths:
  - 'app/**'
---

# App

## API-first Laravel layout
The app is a Sanctum-authenticated REST API. Core code lives in `Http` (Controllers, Form Requests, Resources, Responses), `Models`, `Policies`, and `Services` (AuthService only for now).

## Authorization
Use Policy classes with `$this->authorize()` in every controller action. Ownership checks use `user_id` on portfolios; transactions authorize via the parent portfolio. Admins bypass ownership via `User::isAdmin()`.

## Error responses
Use `App\Http\Responses\ApiErrorResponse` for manual API errors. Exception rendering is centralized in `bootstrap/app.php` — all `api/*` errors return `{ message, errors? }`.
