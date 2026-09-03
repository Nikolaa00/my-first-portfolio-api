---
paths:
  - 'app/Models/**'
---

# Models

## User model uses Sanctum API tokens
The User model uses `HasApiTokens` for API authentication. Add `HasApiTokens` to any other authenticatable models that issue Sanctum tokens.

## User roles
`UserRole` enum: `user`, `admin`. Use `$user->isAdmin()` for role checks in policies.

## Relationships
- User `hasMany` Portfolio
- Portfolio `belongsTo` User, `hasMany` Transaction
- Transaction `belongsTo` Portfolio and Asset

## Casts
Enums: `UserRole`, `Currency`, `TransactionType`, `AssetType`. Password is hashed via cast.
