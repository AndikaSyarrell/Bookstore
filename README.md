# Bookstore

Aplikasi Bookstore adalah platform e-commerce untuk penjualan buku online yang dibangun dengan Laravel dan dilengkapi dengan fitur-fitur modern seperti sistem pembayaran, manajemen inventori, dan live chat.

## Persyaratan Sistem

- PHP 8.1 atau lebih tinggi
- Composer
- Node.js dan NPM
- Database (MySQL, PostgreSQL, atau SQLite)
- Git

## Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd app
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Setup File Konfigurasi

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookstore
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Jalankan Migration

```bash
php artisan migrate
```

### 7. Jalankan Seeder (Opsional)

```bash
php artisan db:seed
```

Atau jalankan seeder tertentu:

```bash
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
```

### 8. Install Dependencies Frontend

```bash
npm install
```

### 9. Build Assets Frontend

```bash
npm run build
```

Atau untuk development dengan hot reload:

```bash
npm run dev
```

### 10. Jalankan Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Struktur Folder

- `app/` - Kode aplikasi (Models, Controllers, Middleware)
- `database/` - Migrations, Factories, Seeders
- `resources/` - Views, CSS, JavaScript
- `routes/` - Definisi routes
- `tests/` - Unit dan Feature tests
- `config/` - File konfigurasi aplikasi
- `storage/` - File uploads dan logs

## Fitur Utama

- 📚 Manajemen Katalog Produk (Buku)
- 🛒 Sistem Keranjang Belanja
- 💳 Integrasi Pembayaran
- 📦 Pelacakan Pengiriman
- 💬 Fitur Live Chat
- 👥 Manajemen Pengguna dan Role
- 📊 Dashboard Admin

## Penggunaan

### Menjalankan Aplikasi

```bash
# Development
php artisan serve
npm run dev

# Production
npm run build
php artisan serve
```

### Akses Database

Menggunakan Tinker (Laravel REPL):

```bash
php artisan tinker
```

### Jalankan Tests

```bash
php artisan test
```

## Troubleshooting

### Masalah Permission Storage

```bash
chmod -R 775 storage bootstrap/cache
```

### Cache Issues

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Database Connection Error

Pastikan:
1. Database server sedang berjalan
2. Kredensial database di file `.env` benar
3. Database dengan nama yang ditentukan sudah ada

## Kontribusi

Untuk berkontribusi pada project ini, silakan buat Pull Request dengan deskripsi yang jelas.

## Support

Jika menemukan masalah, silakan buat Issue di repository ini.
