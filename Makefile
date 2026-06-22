.PHONY: up down build logs migrate seed test backend-shell frontend-shell

up:
	docker compose up --build

down:
	docker compose down

build:
	docker compose build

logs:
	docker compose logs -f

migrate:
	docker compose exec backend-php php artisan migrate --force

seed:
	docker compose exec backend-php php artisan db:seed --force

test:
	docker compose exec backend-php php artisan test

backend-shell:
	docker compose exec backend-php sh

frontend-shell:
	docker compose exec frontend sh
