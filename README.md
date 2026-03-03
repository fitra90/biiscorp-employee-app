# Employee Management System

Sistem manajemen karyawan berbasis Laravel dengan fitur lengkap untuk mengelola data karyawan, termasuk upload foto dan dokumen.

## Fitur Utama

- ✅ **Manajemen Karyawan**: Tambah, edit, hapus, dan lihat data karyawan
- ✅ **Upload Foto**: Drag & drop foto karyawan menggunakan Dropzone.js
- ✅ **Upload Dokumen**: Upload dokumen PDF/DOC dengan input file standar
- ✅ **Pencarian Kolom**: Pencarian individual per kolom dengan Select2
- ✅ **Filter Tanggal**: Filter data berdasarkan rentang tanggal bergabung
- ✅ **Filter Departemen**: Filter berdasarkan departemen
- ✅ **Server-side DataTables**: Penanganan data besar dengan DataTables server-side
- ✅ **Responsive Design**: Tampilan responsif menggunakan Bootstrap 5
- ✅ **Validasi Form**: Validasi form yang komprehensif

## Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **File Upload**: Dropzone.js (foto), input file standar (dokumen)
- **Date Picker**: Date Range Picker
- **Select**: Select2 dengan tema Bootstrap 5
- **Build Tool**: Vite

## Persyaratan Sistem

- PHP ^8.2
- Composer
- Node.js & npm
- MySQL/MariaDB
- Laravel Valet/XAMPP/WAMP (opsional)

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/employee-app.git
cd employee-app
```

### 2. Install Dependencies PHP

```bash
composer install
```

### 3. Install Dependencies JavaScript

```bash
npm install
```

### 4. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan pengaturan database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Setup Database

```bash
php artisan migrate
php artisan db:seed
```

### 6. Build Assets

```bash
npm run build
```

Untuk development:

```bash
npm run dev
```

### 7. Jalankan Aplikasi

```bash
composer run dev
```

Aplikasi akan berjalan di `http://localhost:8000`

## Struktur Database

Tabel `employees`:
- `id` - ID karyawan (auto increment)
- `name` - Nama lengkap karyawan
- `email` - Email karyawan (unique)
- `phone` - Nomor telepon
- `position` - Jabatan
- `department` - Departemen
- `join_date` - Tanggal bergabung
- `photo` - Path foto karyawan (nullable)
- `document` - Path dokumen (nullable)
- `created_at` & `updated_at` - Timestamp

## API Endpoints

- `GET /api/employees/table` - Data untuk DataTables (server-side)
- `POST /api/employees` - Menambah karyawan baru
- `GET /api/employees/{id}` - Detail karyawan
- `PUT /api/employees/{id}` - Update karyawan
- `DELETE /api/employees/{id}` - Hapus karyawan

## Cara Penggunaan

### Menambah Karyawan Baru

1. Klik tombol "Add New Employee"
2. Isi form dengan data yang diperlukan
3. Upload foto dengan drag & drop atau klik area upload
4. Upload dokumen (PDF/DOC/DOCX) jika diperlukan
5. Klik "Save Employee"

### Mencari Karyawan

- Gunakan kotak pencarian di atas tabel untuk pencarian global
- Gunakan kotak pencarian di header kolom untuk pencarian per kolom
- Gunakan filter tanggal untuk mencari berdasarkan rentang tanggal bergabung
- Gunakan filter departemen untuk mencari berdasarkan departemen

### Filter Data

- **Date Range**: Pilih rentang tanggal di filter atas tabel
- **Department**: Pilih departemen dari dropdown filter
- **Reset Filter**: Klik tombol "Reset" untuk menghapus semua filter

## Konfigurasi Upload File

### Foto Karyawan
- Format: JPG, PNG, GIF
- Maksimal: 1 file
- Ukuran maksimal: Sesuai konfigurasi PHP (default 2MB)
- Lokasi penyimpanan: `storage/app/public/photos`

### Dokumen
- Format: PDF, DOC, DOCX
- Maksimal: 1 file
- Lokasi penyimpanan: `storage/app/public/documents`

## Troubleshooting

### File Upload Gagal
- Pastikan direktori `storage/app/public` memiliki permission yang benar
- Jalankan `php artisan storage:link` untuk membuat symlink
- Cek konfigurasi `upload_max_filesize` di php.ini

### DataTables Error
- Pastikan CSRF token sudah benar
- Cek console browser untuk error JavaScript
- Pastikan API endpoint dapat diakses

### Build Error
- Hapus folder `node_modules` dan jalankan `npm install` ulang
- Pastikan Node.js versi terbaru
- Cek file `vite.config.js` untuk konfigurasi yang benar

## License

Proyek ini open source dan tersedia di bawah [MIT License](LICENSE).

## Kontribusi

Kontribusi sangat dipersilakan! Silakan buka issue atau pull request.

## Support

Jika mengalami masalah, silakan:
1. Buka issue di GitHub
2. Sertakan langkah reproduksi error
3. Sertakan screenshot jika diperlukan

---

**Catatan**: Pastikan untuk menjalankan `composer run dev` untuk menjalankan aplikasi ini.