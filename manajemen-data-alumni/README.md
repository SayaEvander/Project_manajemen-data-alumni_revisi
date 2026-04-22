# 🏫 Manajemen Data Alumni

Aplikasi web sederhana untuk mengelola data alumni sekolah menggunakan PHP Native dan MySQL.
Proyek ini mendukung operasi CRUD (Create, Read, Update, Delete) dan akan dikembangkan lebih lanjut dengan fitur multi-level user (Admin, Super Admin, User).

------------------------------------------------------------
🚀 Fitur
------------------------------------------------------------
- Tampilkan daftar data alumni
- Tambah data alumni baru
- Edit data alumni
- Hapus data alumni
- (Dalam pengembangan) Login multi-level user
- Desain sederhana dengan native CSS

------------------------------------------------------------
🗂️ Struktur Folder
------------------------------------------------------------
```
/manajemen-data-alumni
│
├── koneksi.php          # File koneksi ke database
├── dashboard.php        # Halaman utama daftar alumni
├── tambah.php           # Form tambah data alumni
├── edit.php             # Form edit data alumni
├── delete.php           # Proses hapus data alumni
├── index.php            # Halaman login
├── style/               # Semua CSS
├── screenshots/         # Preview Tampilan Website
└── database/            # Database
```

------------------------------------------------------------
🧰 Teknologi yang Digunakan
------------------------------------------------------------
- PHP 8+ (Native)
- MySQL / MariaDB
- HTML5 & CSS3
- XAMPP (untuk server lokal)

------------------------------------------------------------
⚙️ Cara Menjalankan di Lokal
------------------------------------------------------------
1. Clone repository:
   git clone https://github.com/BintangSry/manajemen-data-alumni.git

2. Pindahkan folder proyek ke direktori htdocs XAMPP:
   C:\xampp\htdocs\manajemen-data-alumni

3. Jalankan XAMPP, lalu start Apache dan MySQL

4. Buka phpMyAdmin, buat database baru:
   db_alumni

5. Import file db_alumni.sql ke database tersebut

6. Jalankan di browser:
   http://localhost/manajemen-data-alumni/

------------------------------------------------------------
📸 Preview Tampilan
------------------------------------------------------------
![Preview Website](screenshots/dashboard.png)  

------------------------------------------------------------
💡 Rencana Pengembangan
------------------------------------------------------------
- Sistem login multi-level user (Admin, Super Admin, User)
- Pencarian & filter data alumni
- Desain ulang menggunakan Tailwind CSS
- Export data ke Excel / PDF
- Responsive layout untuk mobile

------------------------------------------------------------
🔐 LOGIN DEFAULT
------------------------------------------------------------

| Username        | Password  |
|-------------|---------- |
| superadmin  | superadmin |
| admin       | admin  |
| user        | user   |

💡 Kamu bisa mengedit atau menambahkan akun baru melalui database.

---
👨‍💻 Author
------------------------------------------------------------
Bintang Surya Nugraha
Fullstack Developer | PHP & React Enthusiast

Email: bintangsry31@gmail.com
GitHub: https://github.com/BintangSry

------------------------------------------------------------

Made with ❤️ using PHP Native
