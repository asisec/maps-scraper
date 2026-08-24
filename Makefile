.PHONY: build up down restart logs ps shell-backend shell-mongo setup fresh

build:
	docker-compose build --no-cache

up:
	docker-compose up -d

down:
	docker-compose down

restart:
	docker-compose down && docker-compose up -d

logs:
	docker-compose logs -f

ps:
	docker-compose ps

shell-backend:
	docker-compose exec backend sh

shell-mongo:
	docker-compose exec mongodb mongosh -u root -p secret

setup:
	docker-compose build
	docker-compose up -d
	docker-compose exec backend php artisan key:generate
	docker-compose exec backend php artisan migrate --seed || true
	@echo "Kurulum tamamlandi. http://localhost:4200 adresini ziyaret edin."

fresh:
	docker-compose down -v
	docker-compose build --no-cache
	docker-compose up -d
	docker-compose exec backend php artisan key:generate
	@echo "Temiz kurulum tamamlandi."
