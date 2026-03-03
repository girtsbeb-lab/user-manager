# 🧑‍💻 User Manager — Laravel + PostgreSQL + Vue 3 + Inertia + Vuetify

Pilnvērtīga CRUD web aplikācija, kas importē lietotāju datus no [RandomUser API](https://randomuser.me/api/?results=50) un glabā tos PostgreSQL datubāzē trīs saistītās tabulās.

---

## 🏗️ Tehnoloģijas

| Layer | Stack |
|---|---|
| Backend | PHP 8.2, Laravel 10 |
| Datubāze | PostgreSQL 15 |
| Frontend | Vue 3, Inertia.js, Vuetify 3 |
| Build | Vite 5 |
| Container | Docker, Docker Compose, Nginx |

---

## 📁 Datubāzes struktūra

```
users
├── id, external_id
├── first_name, last_name, email, username
├── gender, date_of_birth, age, nationality
├── avatar, thumbnail
└── timestamps, deleted_at

contacts (belongs to users)
├── id, user_id (FK)
├── phone, cell, email
└── timestamps, deleted_at

addresses (belongs to users)
├── id, user_id (FK)
├── street_number, street_name, city, state, country, postcode
├── latitude, longitude
├── timezone_offset, timezone_description
└── timestamps, deleted_at
```

---

## 🚀 Ātrā uzstādīšana ar Docker

### Priekšnosacījumi
- Docker Desktop ≥ 24
- Docker Compose v2
- Make (neobligāti, bet ērti)

### 1. Klonēt repozitoriju
```bash
git clone https://github.com/<YOUR_USERNAME>/user-manager.git
cd user-manager
```

### 2. Uzstādīt projektu
```bash
# Visa uzstādīšana vienā komandā:
make install

# VAI manuāli:
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
docker compose run --rm node
```

### 3. Atvērt pārlūkā
```
http://localhost:8080
```

---

## ⚡ Importēšana no komandrindas

```bash
# Importēt 50 lietotājus (noklusējums)
php artisan users:import

# Importēt 100 lietotājus
php artisan users:import --results=100

# Izdzēst visus datus un importēt no jauna
php artisan users:import --truncate

# Ar Docker:
docker compose exec app php artisan users:import --results=200
make import ARGS="--results=100"
make import-fresh
```

---

## 🌐 Frontend iespējas

- **`/users`** — Lietotāju saraksts ar meklēšanu, filtrēšanu (dzimums, nacionalitāte), pagināciju
- **`/users/create`** — Izveidot jaunu lietotāju
- **`/users/{id}`** — Lietotāja profils ar kontaktu un adresi
- **`/users/{id}/edit`** — Rediģēt lietotāja datus
- **`/users/{id}/contact/edit`** — Rediģēt kontaktinformāciju
- **`/users/{id}/address/edit`** — Rediģēt adresi
- **Import poga** — tieši no UI importēt jaunus datus, norādot skaitu un vai dzēst esošos

---

## 🐳 Docker konteineri

| Konteineris | Apraksts | Ports |
|---|---|---|
| `laravel_app` | PHP 8.2-FPM + Laravel | — |
| `laravel_nginx` | Nginx web server | `8080:80` |
| `laravel_postgres` | PostgreSQL 15 | `5432:5432` |
| `laravel_redis` | Redis (sessions/cache) | — |

---

## 📋 Make komandas

```bash
make help         # Rādīt visas komandas
make up           # Startēt konteinerus
make down         # Apturēt konteinerus
make build        # Pārbūvēt konteinerus
make install      # Uzstādīt projektu no nulles
make migrate      # Palaist migrācijas
make fresh        # Dzēst un pārveidot tabulas
make import       # Importēt lietotājus
make import-fresh # Importēt ar tīru datubāzi
make shell        # Ieiet app konteinerā
make logs         # Sekot logiem
```

---

## 📸 Aplikācijas struktūra

```
├── app/
│   ├── Console/Commands/ImportUsers.php    ← Artisan komanda
│   ├── Http/Controllers/
│   │   ├── UserController.php             ← Lietotāju CRUD
│   │   ├── ContactController.php          ← Kontaktu CRUD
│   │   └── AddressController.php          ← Adrešu CRUD
│   ├── Models/
│   │   ├── User.php
│   │   ├── Contact.php
│   │   └── Address.php
│   └── Services/
│       └── RandomUserService.php          ← API logika
├── database/migrations/
│   ├── create_users_table.php
│   ├── create_contacts_table.php
│   └── create_addresses_table.php
├── resources/js/
│   ├── Pages/
│   │   ├── Users/{Index,Create,Edit,Show}.vue
│   │   ├── Contacts/Edit.vue
│   │   └── Addresses/Edit.vue
│   └── Layouts/AppLayout.vue
├── routes/web.php
├── docker-compose.yml
├── docker/
│   ├── php/Dockerfile
│   └── nginx/default.conf
└── Makefile
```

---

## 🔧 Konfigurācija (.env)

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel_users
DB_USERNAME=laravel
DB_PASSWORD=secret

RANDOM_USER_API=https://randomuser.me/api/
RANDOM_USER_RESULTS=50
```

---

## 📝 GitHub uzstādīšana

```bash
git init
git add .
git commit -m "feat: initial Laravel + Vue + PostgreSQL + Docker setup"
git remote add origin https://github.com/<USERNAME>/user-manager.git
git push -u origin main
```
