# 🧑‍💻 User Manager — Laravel + PostgreSQL + Vue 3 + Inertia + Vuetify

CRUD web aplikācija, kas importē lietotāju datus no RandomUser API un glabā tos PostgreSQL datubāzē.

## 🏗️ Tehnoloģijas
- **Backend:** PHP 8.2, Laravel 10
- **Datubāze:** PostgreSQL 15
- **Frontend:** Vue 3, Inertia.js, Vuetify 3
- **Build:** Vite 5
- **Container:** Docker, Nginx

## ⚡ Ātrā uzstādīšana (1 komanda)

git clone https://github.com/girtsbeb-lab/user-manager.git
cd user-manager
./setup.sh

Skripts automātiski veic visus uzstādīšanas soļus un beigās atver `http://localhost:8080`.

---

## 🚀 Uzstādīšana ar Docker

### Priekšnosacījumi
- Docker Desktop
- Git

### 1. Klonēt repozitoriju
```bash
git clone https://github.com/girtsbeb-lab/user-manager.git
cd user-manager
```

### 2. Uzstādīt vidi
```bash
cp .env.example .env
```

### 3. Palaist konteinerus
```bash
docker compose up -d --build
```

### 4. Instalēt Redis PHP extension
```bash
docker compose exec app bash -c "echo 'no' | pecl install redis && docker-php-ext-enable redis"
docker compose restart app
```

### 5. Instalēt Laravel dependencies
```bash
docker compose exec app composer install
```

### 6. Kopēt Laravel 10 middleware failus
```bash
docker compose exec app composer create-project laravel/laravel:^10.0 /tmp/laravel10 --prefer-dist
docker compose exec app cp /tmp/laravel10/app/Http/Middleware/*.php /var/www/app/Http/Middleware/
docker compose exec app rm -rf /var/www/app/Http/Middleware/Middleware
docker compose exec app composer dump-autoload
```

### 7. Uzstādīt aplikāciju
```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

### 8. Pārbūvēt frontend
```bash
docker compose run --rm node
```

### 9. Importēt lietotājus
```bash
docker compose exec app php artisan users:import
```

### 10. Atvērt pārlūkā
```
http://localhost:8080
```

## 📋 Noderīgas komandas
```bash
# Importēt 50 lietotājus (noklusējums)
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
