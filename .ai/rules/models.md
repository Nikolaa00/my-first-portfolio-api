---
paths:
  - 'app/Models/**'
---

# Models

## User model uses Sanctum API tokens
The User model uses HasApiTokens for API authentication. Add HasApiTokens to any other authenticatable models that issue Sanctum tokens.
