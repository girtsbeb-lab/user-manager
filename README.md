# 🧑‍💻 User Manager — Laravel + PostgreSQL + Vue 3 + Inertia + Vuetify

CRUD web aplikācija, kas importē lietotāju datus no RandomUser API un glabā tos PostgreSQL datubāzē.

## 🏗️ Tehnoloģijas
- **Backend:** PHP 8.2, Laravel 10
- **Datubāze:** PostgreSQL 15
- **Frontend:** Vue 3, Inertia.js, Vuetify 3
- **Build:** Vite 5
- **Container:** Docker, Nginx

## 🚀 Uzstādīšana ar Docker

### Priekšnosacījumi
- Docker Desktop
- Git

### 1. Klonēt repozitoriju
```bash
git clone https://github.com/girtsbeb-lab/user-manager.git
cd user-manager
```

### 2. Palaist konteinerus
```bash
cp .env.example .env
docker compose up -d --build
```

### 3. Instalēt Redis PHP extension
```bash
docker compose exec app bash -c "pecl install redis && docker-php-ext-enable redis"
docker compose restart app
```

### 4. Instalēt Laravel dependencies
```bash
docker compose exec app composer install
```

### 5. Kopēt Laravel 10 middleware failus
```bash
docker compose exec app composer create-project laravel/laravel:^10.0 /tmp/laravel10 --prefer-dist
docker compose exec app cp -r /tmp/laravel10/app/Http/Middleware /var/www/app/Http/Middleware
docker compose exec app composer dump-autoload
```

### 6. Uzstādīt aplikāciju
```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

### 7. Pārbūvēt frontend
```bash
docker compose run --rm node
```

### 8. Importēt lietotājus
```bash
docker compose exec app php artisan users:import
```

### 9. Atvērt pārlūkā
```
http://localhost:8080
```

## 📋 Komandas
```bash
# Importēt 50 lietotājus
docker compose exec app php artisan users:import

# Importēt 100 lietotājus
docker compose exec app php artisan users:import --results=100

# Dzēst visus un importēt no jauna
docker compose exec app php artisan users:import --truncate

# Apturēt konteinerus
docker compose down

# Startēt konteinerus
docker compose up -d
```

## 🌐 Lapas
- `/users` — Lietotāju saraksts ar meklēšanu un filtriem
- `/users/create` — Izveidot jaunu lietotāju
- `/users/{id}` — Lietotāja profils
- `/users/{id}/edit` — Rediģēt lietotāju
- `/users/{id}/contact/edit` — Rediģēt kontaktinformāciju
- `/users/{id}/address/edit` — Rediģēt adresi

## 🐳 Docker konteineri
| Konteineris | Apraksts | Ports |
|---|---|---|
| `laravel_app` | PHP 8.2-FPM + Laravel | — |
| `laravel_nginx` | Nginx | `8080:80` |
| `laravel_postgres` | PostgreSQL 15 | `5432:5432` |
| `laravel_redis` | Redis | — |
