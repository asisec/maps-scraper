.PHONY: build up down restart logs ps shell-backend shell-mongo setup fresh test lint docs seed

build:
	docker-compose build

up:
	docker-compose up -d
	@echo "Servisler baslatildi: Frontend: http://localhost:4200 | Backend: http://localhost:8000"

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

docs:
	docker-compose exec -T backend php artisan l5-swagger:generate
	@echo "Swagger dokumantasyonu olusturuldu: http://localhost:8000/api/documentation"

seed:
	docker-compose exec -T backend php artisan db:seed --force
	@echo "Ornek veriler yuklendi."

test:
	docker-compose exec -T backend php artisan test
	@echo "Backend testleri basariyla tamamlandi."

lint:
	cd frontend && npm run lint

setup:
	docker-compose build
	docker-compose up -d
	docker-compose exec -T backend php artisan key:generate --force
	docker-compose exec -T backend php artisan migrate --seed --force || true
	docker-compose exec -T backend php artisan l5-swagger:generate
	@echo "Kurulum basariyla tamamlandi! Uygulamaya http://localhost:4200 adresinden ulasabilirsiniz."

fresh:
	docker-compose down -v
	docker-compose build --no-cache
	docker-compose up -d
	docker-compose exec -T backend php artisan key:generate --force
	docker-compose exec -T backend php artisan l5-swagger:generate
	@echo "Sifirdan temiz kurulum basariyla tamamlandi."