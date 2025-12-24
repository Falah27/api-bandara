# Panduan Mengatasi Error Upload File Besar

## Masalah Yang Terjadi
- Upload file Excel Januari-November gagal dengan error koneksi
- Upload file Desember berhasil karena lebih kecil

## Penyebab
1. **File terlalu besar** - Januari-November berisi lebih banyak data
2. **Timeout PHP** - Proses melebihi batas waktu default (30 detik)
3. **Memory limit** - PHP kehabisan memori saat memproses data besar

## Solusi Yang Sudah Diterapkan

### 1. Optimasi Kode (✅ Sudah Selesai)
- ✅ Batch processing (insert per 100 row, bukan 1-1)
- ✅ Commit bertahap untuk menghindari lock database
- ✅ Memory limit dinaikkan ke 512MB
- ✅ Execution time dinaikkan ke 300 detik (5 menit)
- ✅ File upload limit dinaikkan ke 20MB

### 2. Konfigurasi XAMPP Yang Perlu Dicek

#### A. File `php.ini` XAMPP
Lokasi: `C:\xampp\php\php.ini`

Cari dan ubah nilai berikut:
```ini
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

**Cara Edit:**
1. Buka XAMPP Control Panel
2. Klik tombol "Config" di samping Apache
3. Pilih "PHP (php.ini)"
4. Cari baris-baris di atas (gunakan Ctrl+F)
5. Ubah nilainya
6. Simpan file
7. **Restart Apache** dari XAMPP Control Panel

#### B. File `.htaccess` (Opsional)
Jika php.ini tidak bekerja, copy file `.htaccess.upload` ke folder `public/` dan rename jadi `.htaccess`

### 3. Test Upload

Setelah restart Apache, coba upload lagi file Januari-November.

#### Perkiraan Waktu
- File 10MB ~ 2-3 menit
- File 20MB ~ 4-5 menit

## Tips Troubleshooting

### Jika Masih Error Timeout:
1. **Pecah file menjadi lebih kecil**
   - Upload Januari-Juni terpisah
   - Upload Juli-November terpisah
   
2. **Cek log error**
   - `storage/logs/laravel.log`
   - `C:\xampp\php\logs\php_error_log`

### Jika Error "Out of Memory":
Naikkan `memory_limit` di `php.ini` ke `1024M` (1GB)

### Jika Data Duplikat:
Kode sudah dilengkapi deteksi duplikat berdasarkan:
- Airport ID sama
- Tanggal sama
- Deskripsi sama

Data duplikat akan dilewati otomatis.

## Monitoring Progress

Respons API akan memberi tahu:
```json
{
  "message": "Selesai. 1500 data baru berhasil diimpor. (250 data dilewati karena sudah ada).",
  "errors": []
}
```

## Kontak
Jika masih ada masalah, screenshot error yang muncul beserta file log.
