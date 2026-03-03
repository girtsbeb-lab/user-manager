.PHONY: help up down build install migrate seed import fresh logs shell

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

build: ## Build and start containers
	docker compose up -d --build

install: ## Install all dependencies and setup project
	docker compose exec app composer install
	docker compose exec app cp .env.example .env
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan migrate
	docker compose run --rm node

migrate: ## Run database migrations
	docker compose exec app php artisan migrate

fresh: ## Fresh migration (drops all tables)
	docker compose exec app php artisan migrate:fresh

import: ## Import users from API (usage: make import ARGS="--results=100")
	docker compose exec app php artisan users:import $(ARGS)

import-fresh: ## Import with truncate (fresh data)
	docker compose exec app php artisan users:import --truncate

logs: ## Show app logs
	docker compose logs -f app

shell: ## Open shell in app container
	docker compose exec app bash

npm-dev: ## Run Vite dev server
	docker compose exec app npm run dev

npm-build: ## Build frontend assets
	docker compose run --rm node
