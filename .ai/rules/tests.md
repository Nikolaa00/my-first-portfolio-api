---
paths:
  - 'tests/**'
---

# Tests

## PHPUnit test framework
This project uses PHPUnit class-based tests, not Pest. Extend `Tests\TestCase` for feature tests that hit the application.

## Feature test suites
- `PortfolioTest` — portfolio CRUD, ownership, validation
- `TransactionTest` — nested/shallow transaction CRUD, ownership
- `AdminTest` — admin user listing, 403 for regular users
- `ApiErrorTest` — unified JSON error format (401, 403, 404, 422)

## Test conventions
Use `RefreshDatabase`, model factories, and Sanctum `$user->createToken('api')->plainTextToken` for authenticated requests. Run tests via Sail: `./vendor/bin/sail artisan test --compact`.
