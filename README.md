# Fantasy Academy

## Development
Simply run `docker compose up`

- Frontend runs at http://localhost:3000  
- API runs at http://localhost:8080

Create your admin user run (replace email+password placeholders):
```bash
docker compose exec api bin/console app:user:register admin@admin.com admin
```

### Adminer (Database)

http://localhost:8000

Driver: `postgres`  
User: `postgres`  
Password: `postgres`  
Database: `fantasy_academy`

### Mail catcher

http://localhost:8025
