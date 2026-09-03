---
paths:
  - 'app/Http/**'
---

# Http

## JSON API only
API requests under `api/*` and JSON-expecting requests render exceptions as JSON. Build API endpoints to return JSON, not Blade views.

## Form Requests
Validation lives in Form Request classes under `app/Http/Requests/{Domain}/`. Every request must define `rules()`, `messages()`, and `attributes()`. Authorization for resource access belongs in Policies, not Form Requests (`authorize()` returns `true`).

## API Resources
Use Eloquent API Resources for response shaping (`UserResource`, `PortfolioResource`, `TransactionResource`, `AuthTokenResource`). Do not expose passwords or hidden model attributes.

## Unified errors
Manual errors use `ApiErrorResponse::make($message, $status, $errors = [])`. Validation and framework exceptions are formatted automatically for `api/*` routes.
