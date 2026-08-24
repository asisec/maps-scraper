# Harita Kaziyici

Belirli bir cografik alan icindeki Google Haritalar isletme verilerini kaziyarak XLSX, PDF ve Gorsel formatlarinda disa aktaran web uygulamasi.

## Teknoloji Altyapisi

| Katman | Teknoloji |
|---|---|
| Arka Yuz | PHP Laravel 12 |
| On Yuz | AngularJS 1.8 |
| Veritabani | MongoDB 7.0 |
| Konteyner | Docker + Docker Compose |
| CI/CD | GitHub Actions |
| API Dokumantasyonu | Swagger UI |

## Hizli Baslangic

### Gereksinimler

- Docker Engine (v24+)
- Docker Compose (v2+)
- Git

### 1. Depoyu Klonlayin

```bash
git clone https://github.com/asisec/maps-scraper.git
cd maps-scraper
```

### 2. Ortam Degiskenlerini Hazirlayin

```bash
cp backend/.env.example backend/.env
```

`backend/.env` dosyasini acin ve asagidaki degeri doldurun:

```
GOOGLE_MAPS_JS_API_KEY=buraya_harita_goruntusu_icin_api_anahtarinizi_yazin
```

### 3. Uygulamayi Baslatma (Tek Komut)

```bash
make setup
```

Bu komut sirasyla:
1. Docker imajlarini olusturur
2. Konteynerleri baslatir
3. Laravel uygulama anahtarini uretir
4. Veritabani koleksiyonlarini hazirlar

### 4. Uygulamaya Erisim

| Servis | Adres |
|---|---|
| On Yuz (Kullanici Arayuzu) | http://localhost:4200 |
| Arka Yuz API | http://localhost:8000 |
| Swagger UI | http://localhost:8000/api/documentation |

## Docker Komutlari

```bash
make build       # Docker imajlarini olusturur
make up          # Konteynerleri baslatir (arka planda)
make down        # Konteynerleri durdurur
make restart     # Yeniden baslatir
make logs        # Canli log akisi
make ps          # Konteyner durumlarini listeler
make shell-backend   # Backend konteyneri icine girer
make shell-mongo     # MongoDB kabugundan baglanir
make fresh       # Tum verileri silerek temiz kurulum yapar
```

## Harita Arayuzu Kullanimi

1. **http://localhost:4200/#/panel** adresine gidin
2. Harita uzerinde bir daire cizmek icin daire aracini secin
3. Taramak istediginiz alani daire ile isaretleyin
4. **Taramayi Baslatl** dugmesine tiklayin
5. Sonuclar tabloda listelendikten sonra su formatlarda indirin:
   - **Excel Olarak Indir** — .xlsx dosyasi
   - **PDF Olarak Indir** — .pdf dosyasi
   - **Resim Olarak Indir** — .png dosyasi

## Swagger API Dokumantasyonu

Arka yuz API endpoint'leri tam dokumantasyon ile birlikte gelir.

Swagger UI adresine gidin: http://localhost:8000/api/documentation

Mevcut endpoint'ler:
- `POST /api/scrape` — Tarama islemini baslatir
- `GET /api/export/excel` — Excel dosyasi indirir
- `GET /api/export/pdf` — PDF dosyasi indirir
- `GET /api/export/image` — Gorsel dosya indirir

## Proje Yapisi

```
maps-scraper/
├── backend/                 # Laravel PHP arka yuzu
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Services/
│   │   └── Models/
│   ├── routes/api.php
│   ├── Dockerfile
│   └── .env.example
├── frontend/                # AngularJS on yuzu
│   ├── app/
│   │   ├── controllers/
│   │   ├── services/
│   │   └── views/
│   ├── index.html
│   ├── nginx.conf
│   └── Dockerfile
├── docker/
│   └── mongo/
│       └── init.js          # MongoDB ilk kurulum scripti
├── .github/
│   └── workflows/
│       └── main.yml         # CI/CD pipeline
├── docker-compose.yml
├── Makefile
└── README.md
```

## CI/CD Pipeline

Uygulamaya herhangi bir commit yapildiginda veya `main` branch'ine pull request acildiginda GitHub Actions otomatik olarak:

1. PHP 8.2 ortaminda Laravel testlerini calistirir
2. Node.js 20 ile ESLint kontrolu yapar ve frontend'i derler
3. Her iki Docker imajini da basariyla derledigini dogrular

## Lisans

MIT
