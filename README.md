# Prime Capital API

REST API for managing investment portfolios, transactions, and user accounts. Built with **Laravel 13**, **PostgreSQL**, **Laravel Sanctum** (Bearer tokens), and **Laravel Sail** (Docker).

**Repository:** [github.com/Nikolaa00/my-first-portfolio-api](https://github.com/Nikolaa00/my-first-portfolio-api)

---

## Tech stack

| Layer | Technology |
| --- | --- |
| Framework | Laravel 13, PHP 8.5 |
| Database | PostgreSQL 18 |
| Auth | Laravel Sanctum (API tokens) |
| Containers | Laravel Sail (Docker Compose) |
| Testing | PHPUnit |
| API client | Bruno (`bruno/` collection) |
| Code style | Laravel Pint |

---

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/) (optional if you run everything via Sail)

---

## Quick start (Sail)

```bash
git clone https://github.com/Nikolaa00/my-first-portfolio-api.git
cd my-first-portfolio-api

composer install

cp .env.example .env
php artisan key:generate

./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

API base URL (Sail default): **http://localhost/api**

Health check:

```bash
curl http://localhost/api/health/database
```

---

## Docker containers

Defined in `compose.yaml`:

| Service | Image | Host port | Purpose |
| --- | --- | --- | --- |
| `laravel.test` | `sail-8.5/app` | **80** (API), 5173 (Vite) | Laravel application |
| `pgsql` | `postgres:18-alpine` | 5432 | PostgreSQL database |

Common Sail commands:

```bash
./vendor/bin/sail up -d              # Start containers
./vendor/bin/sail down               # Stop containers
./vendor/bin/sail artisan migrate    # Run migrations
./vendor/bin/sail artisan db:seed    # Seed database
./vendor/bin/sail artisan test       # Run tests
./vendor/bin/sail shell              # Shell into app container
```

---

## Environment variables (`.env.example`)

Copy `.env.example` to `.env` before starting the app.

| Variable | Default | Description |
| --- | --- | --- |
| `APP_NAME` | Prime Capital API | Application name |
| `APP_URL` | http://localhost | Base URL (Sail serves on port 80) |
| `APP_KEY` | *(empty)* | Generated via `php artisan key:generate` |
| `APP_DEBUG` | true | Debug mode (set `false` in production) |
| `DB_CONNECTION` | pgsql | Database driver |
| `DB_HOST` | pgsql | Docker service name (use `127.0.0.1` without Sail) |
| `DB_PORT` | 5432 | PostgreSQL port |
| `DB_DATABASE` | prime_capital_database | Database name |
| `DB_USERNAME` | sail | Database user |
| `DB_PASSWORD` | password | Database password |

PHPUnit uses a separate **`testing`** database (configured in `phpunit.xml` and created automatically by Sail's PostgreSQL init script).

---

## Database seeders

Run with:

```bash
./vendor/bin/sail artisan db:seed
```

| Seeder | What it creates |
| --- | --- |
| `AssetSeeder` | 5 sample assets (AAPL, MSFT, GOOGL, BTC, etc.) |
| `UserSeeder` | `test@example.com` (user), `admin@example.com` (admin), 3 random users |
| `PortfolioSeeder` | 2 portfolios per user |
| `TransactionSeeder` | Sample buy/sell transactions per portfolio |

**Seeded credentials** (password for all: `password`):

| Email | Role |
| --- | --- |
| test@example.com | user |
| admin@example.com | admin |

---

## Factories

Located in `database/factories/`:

| Factory | Model | Notes |
| --- | --- | --- |
| `UserFactory` | User | Default password: `password`; `admin()` state |
| `AssetFactory` | Asset | Random asset from predefined list |
| `PortfolioFactory` | Portfolio | Links to `UserFactory` by default |
| `TransactionFactory` | Transaction | Links to portfolio + asset; random buy/sell |

Used in tests and seeders. Example in Tinker:

```bash
./vendor/bin/sail artisan tinker
>>> App\Models\Portfolio::factory()->for(App\Models\User::factory())->create();
```

---

## API routes

All routes are prefixed with `/api`.

### Public

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/health/database` | Database connectivity check |
| POST | `/register` | Register + receive Bearer token |
| POST | `/login` | Login + receive Bearer token |

### Authenticated (Bearer token required)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/me` | Current user profile |
| POST | `/logout` | Revoke current token |
| GET | `/portfolios` | List portfolios (own only; admin sees all) |
| POST | `/portfolios` | Create portfolio |
| GET | `/portfolios/{id}` | Show portfolio |
| PUT | `/portfolios/{id}` | Update portfolio |
| DELETE | `/portfolios/{id}` | Delete portfolio |
| GET | `/portfolios/{id}/transactions` | List transactions for portfolio |
| POST | `/portfolios/{id}/transactions` | Create transaction |
| GET | `/transactions/{id}` | Show transaction (shallow route) |
| PUT | `/transactions/{id}` | Update transaction |
| DELETE | `/transactions/{id}` | Delete transaction |
| GET | `/admin/users` | List all users (**admin only**) |

### Authentication

Send the token on protected routes:

```
Authorization: Bearer {token}
Accept: application/json
```

Login/register return the token in the JSON response body:

```json
{
  "data": {
    "user": { "id": 1, "name": "...", "email": "...", "role": "user" },
    "token": "1|...",
    "token_type": "Bearer"
  }
}
```

### Error format

All API errors use a unified JSON shape:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["The portfolio name is required."]
  }
}
```

Validation responses include `errors`; other errors return `message` only.

### Rate limits

| Limiter | Routes | Limit |
| --- | --- | --- |
| `auth` | register, login | 10 requests/min per IP |
| `api` | All authenticated routes | 120 requests/min per user |

---

## Authorization

- **Policies** enforce ownership: users manage their own portfolios and transactions.
- **Admins** can access any portfolio and the `/admin/users` endpoint.
- `$this->authorize()` is called in every controller action.

---

## Bruno API collection

Open the `bruno/` folder in [Bruno](https://www.usebruno.com/).

1. Select the **Local** environment (`baseUrl: http://localhost/api`).
2. Run **Login** or **Register** to save `{{token}}`.
3. Run **Create Portfolio** to save `{{portfolioId}}`.
4. Run **Create Transaction** to save `{{transactionId}}`.

Folders: **Health**, **Auth**, **Portfolios**, **Transactions**, **Admin**.

---

## Testing

```bash
./vendor/bin/sail artisan test
# or a single file:
./vendor/bin/sail artisan test --compact tests/Feature/PortfolioTest.php
```

| Test file | Coverage |
| --- | --- |
| `PortfolioTest` | Portfolio CRUD, ownership, validation, 401/403 |
| `TransactionTest` | Nested/shallow transaction CRUD, ownership, validation |
| `AdminTest` | Admin user listing, 403 for regular users |
| `ApiErrorTest` | Unified error JSON for 401, 403, 404, 422 |
| `Unit/ExampleTest` | PHPUnit smoke test |

Tests use `RefreshDatabase`, factories, and Sanctum tokens.

Format code before committing:

```bash
vendor/bin/pint --dirty
```

---

## Git & GitHub

```bash
# Clone
git clone https://github.com/Nikolaa00/my-first-portfolio-api.git
cd my-first-portfolio-api

# Check status
git status

# Create a feature branch (recommended)
git checkout -b feature/my-feature

# Commit
git add .
git commit -m "Describe your change"

# Push
git push -u origin feature/my-feature
```

**Remote:** `origin` → `https://github.com/Nikolaa00/my-first-portfolio-api.git`  
**Default branch:** `main`

---

## Project structure

```
app/
  Http/
    Controllers/     # Auth, Portfolio, Transaction, Admin, Health
    Requests/        # Form validation (Auth, Portfolio, Transaction)
    Resources/       # JSON API resources
    Responses/       # ApiErrorResponse (unified errors)
  Models/            # User, Portfolio, Transaction, Asset
  Policies/          # Authorization rules
  Services/          # AuthService
bootstrap/app.php    # Exception handling, JSON errors for API
bruno/               # Bruno API collection
database/
  factories/         # Test/seed data factories
  migrations/        # Schema migrations
  seeders/           # Database seeders
routes/api.php       # API route definitions
tests/Feature/       # HTTP feature tests
compose.yaml         # Sail Docker services
.env.example         # Environment template
```

---

## Laravel Boost (AI tooling)

This project uses [Laravel Boost](https://laravel.com/docs/boost) for AI agent guidelines, skills, and MCP.

| File | Purpose |
| --- | --- |
| `boost.json` | Boost configuration (agents, skills, Sail MCP) |
| `AGENTS.md` / `CLAUDE.md` | AI guidelines (auto-updated) |
| `.ai/rules/` | Project-specific rules for agents |
| `.cursor/mcp.json` | Cursor MCP — runs via Sail |

**Update Boost after package changes:**

```bash
php artisan boost:update --no-interaction
```

**Installed skills:** `infer-conventions`, `laravel-best-practices`, `testing-best-practices`, `tailwindcss-development`

**MCP note:** Boost MCP runs through Sail (`vendor/bin/sail artisan boost:mcp`) so it uses the same PHP/PostgreSQL environment as the app. Ensure containers are running before using Boost tools in Cursor.

---

## License

MIT
