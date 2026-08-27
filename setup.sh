#!/bin/bash
set -e

echo "=== Harita Kaziyici Kurulumu Baslatiliyor ==="

docker-compose build
docker-compose up -d

echo "=== Laravel Ayarlari Yapilandiriliyor ==="
docker-compose exec -T backend php artisan key:generate --force
docker-compose exec -T backend php artisan migrate --seed --force || true
docker-compose exec -T backend php artisan l5-swagger:generate

echo "=== Kurulum Basariyla Tamamlandi ==="
echo "Kullanici Paneli: http://localhost:4200/#/panel"
echo "Yonetim Paneli:  http://localhost:4200/#/admin"
echo "Swagger API:     http://localhost:8000/api/documentation"