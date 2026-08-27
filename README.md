# Harita Kazıyıcı (Maps Scraper)

Belirli bir coğrafi koordinat ve yarıçap alanı içindeki Google Haritalar işletme verilerini (İşletme Adı, Açık Adres, Ortalama Puan, Telefon, E-posta, Web Sitesi) ücretli Places API kullanmadan kazıyan ve toplanan verileri **Excel (XLSX)**, **PDF** ve **Resim (PNG)** formatlarında dışa aktaran web uygulaması.

---

## 🚀 Teknoloji Mimarisi

| Katman | Teknoloji / Kütüphane | Açıklama |
|---|---|---|
| **Arka Yüz (Backend)** | PHP 8.2 & Laravel 12 | RESTful API, Scraper Servisi, Export Servisi |
| **Veritabanı** | MongoDB 7.0 & `mongodb/laravel-mongodb` | NoSQL doküman tabanlı işletme ve görev depolama |
| **Ön Yüz (Frontend)** | AngularJS 1.8 & Bootstrap 5 & FontAwesome 6 | Harita arayüzü, canlı çizim, veri tablosu |
| **Harita Servisi** | Google Maps JavaScript API (Drawing Tools) | Dairesel alan seçimi, koordinat/yarıçap yakalama |
| **Dışa Aktarma** | Maatwebsite Excel, Barryvdh DomPDF, GD Library | XLSX, PDF, PNG formatlarında veri üretimi |
| **API Dokümantasyonu**| Swagger UI (OpenAPI 3.0 / `darkaonline/l5-swagger`) | İnteraktif API test ve dokümantasyon arayüzü |
| **Konteynerizasyon** | Docker & Docker Compose | Çoklu servis mimarisi (Backend, Frontend, MongoDB) |
| **Sürekli Entegrasyon** | GitHub Actions CI/CD | Otomatik testler, ESLint ve Docker derleme doğrulama |

---

## ⚡ Hızlı Başlangıç

### Sistem Gereksinimleri
- **Docker** (v24+) & **Docker Compose** (v2+)
- **Git**

### 1. Depoyu Klonlayın
```bash
git clone https://github.com/asisec/maps-scraper.git
cd maps-scraper
```

### 2. Ortam Değişkenlerini Hazırlayın
```bash
cp backend/.env.example backend/.env
```

### 3. Tek Komutla Kurulum ve Başlatma
```bash
make setup
```
*(Windows PowerShell kullanıcıları `docker-compose up -d --build` veya `make setup` çalıştırabilir.)*

Bu komut sırasıyla:
1. Docker servis imajlarını derler (Backend, Frontend, MongoDB).
2. Konteynerleri arka planda başlatır.
3. Laravel uygulama anahtarını üretir.
4. MongoDB koleksiyonlarını ve indekslerini hazırlar.
5. Swagger API dokümantasyonunu otomatik derler.

---

## 🌐 Uygulama Bağlantıları

| Servis | URL | Açıklama |
|---|---|---|
| **Kullanıcı Paneli** | [http://localhost:4200/#/panel](http://localhost:4200/#/panel) | Harita üzerinden alan seçimi, tarama ve dışa aktarma |
| **Yönetim Paneli** | [http://localhost:4200/#/admin](http://localhost:4200/#/admin) | Yönetici karşılama sayfası |
| **REST API** | [http://localhost:8000/api](http://localhost:8000/api) | Laravel API uç noktaları |
| **Swagger UI** | [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation) | İnteraktif API Dokümantasyonu |

---

## 🗺️ Kullanım Rehberi

1. Tarayıcınızdan **[http://localhost:4200/#/panel](http://localhost:4200/#/panel)** adresine gidin.
2. Haritanın üst orta kısmındaki **Daire Çizim Aracı** butonuna tıklayın.
3. Taramak istediğiniz alanın merkezine tıklayıp sürükleyerek dairesel bir alan çizin.
4. Seçtiğiniz alanın merkez koordinatları ve yarıçapı otomatik olarak tespit edilir.
5. **Taramayı Başlat** butonuna tıklayın.
6. Kazınan işletmeler anında harita üzerinde numaralı işaretçiler (markers) ile gösterilir ve alttaki veri tablosuna yüklenir.
7. İlgili işletmeye haritada odaklanmak için tablodaki harita simgesine tıklayabilirsiniz.
8. Verileri indirmek için tablonun sağ üstündeki butonları kullanabilirsiniz:
   - 📊 **Excel Olarak İndir:** `.xlsx` tablosu indirir.
   - 📄 **PDF Olarak İndir:** Yatay A4 formatında temiz `.pdf` raporu üretir.
   - 🖼️ **Resim Olarak İndir:** Yüksek çözünürlüklü `.png` tablo görseli oluşturur.

---

## 📡 REST API Uç Noktaları

| Metot | Uç Nokta | Açıklama |
|---|---|---|
| `POST` | `/api/scrape` | Dairesel koordinat ve yarıçapa göre işletmeleri kazır ve kaydeder |
| `GET` | `/api/businesses` | Kayıtlı işletmeleri listeler (filtre: `job_id`, `limit`) |
| `GET` | `/api/jobs/{id}` | Tarama görevi durumunu ve istatistiklerini döner |
| `GET` | `/api/export/excel` | İşletme verilerini XLSX olarak indirir |
| `GET` | `/api/export/pdf` | İşletme verilerini PDF olarak indirir |
| `GET` | `/api/export/image` | İşletme verilerini PNG resmi olarak indirir |
| `GET` | `/api/documentation` | Swagger UI dokümantasyon sayfası |

### Örnek Tarama İsteği (`POST /api/scrape`):
```json
{
  "latitude": 39.9334,
  "longitude": 32.8597,
  "radius": 1000
}
```

---

## 🛠️ Geliştirici ve Yönetim Komutları (Makefile)

```bash
make setup          # Sıfırdan tam kurulum ve başlatma
make up             # Konteynerleri arka planda çalıştırır
make down           # Konteynerleri durdurur
make restart        # Servisleri yeniden başlatır
make logs           # Canlı Docker log akışı
make test           # Laravel PHPUnit özellik ve birim testlerini çalıştırır
make lint           # Frontend ESLint kontrolünü çalıştırır
make docs           # Swagger dokümantasyonunu yeniden derler
make shell-backend  # Backend konteyneri içine kabuk oturumu açar
make shell-mongo    # MongoDB mongosh kabuğuna bağlanır
make fresh          # Tüm veritabanı ve imajları sıfırlayarak temiz kurulum yapar
```

---

## 🧪 Testleri Çalıştırma

### Arka Yüz (Backend) Testleri
```bash
docker-compose exec -T backend php artisan test
```

### Ön Yüz (Frontend) Lint Kontrolü
```bash
cd frontend && npm run lint
```

---

## 📁 Proje Dizin Yapısı

```
maps-scraper/
├── backend/                        # Laravel 12 REST API
│   ├── app/
│   │   ├── Exports/               # Excel dışa aktarma sınıfları
│   │   ├── Http/Controllers/Api/  # Scraper ve Export API kontrolcüleri
│   │   ├── Models/                # MongoDB Business ve ScrapeJob modelleri
│   │   └── Services/              # ScraperService ve ExportService
│   ├── config/                    # database, services, l5-swagger yapılandırmaları
│   ├── resources/views/exports/   # DomPDF şablonları
│   ├── routes/api.php             # API rota tanımlamaları
│   ├── tests/Feature/             # ScraperApiTest suite
│   ├── Dockerfile
│   └── .env.example
├── frontend/                       # AngularJS 1.8 SPA
│   ├── app/
│   │   ├── controllers/           # PanelController, AdminController
│   │   ├── services/              # ScraperService API istemcisi
│   │   ├── views/                 # panel.html, admin.html
│   │   ├── app.js                 # Rota ve modül tanımlamaları
│   │   └── app.css                # Özel tema ve harita stilleri
│   ├── index.html                 # Ana giriş sayfası
│   ├── nginx.conf                 # Nginx web sunucu yapılandırması
│   ├── package.json
│   └── Dockerfile
├── docker/
│   └── mongo/init.js              # MongoDB kullanıcı ve veritabanı başlatma
├── .github/workflows/main.yml      # CI/CD otomasyonu
├── docker-compose.yml              # Konteyner orkestrasyonu
├── Makefile                        # Yönetim ve kurulum kısayolları
├── setup.sh                        # Bash kurulum betiği
└── README.md                       # Proje dokümantasyonu
```

---

## 📄 Lisans

Bu proje **MIT Lisansı** altında sunulmaktadır.