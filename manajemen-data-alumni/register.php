<?php
// 1. Wajib ada di baris paling atas agar tidak stuck
ob_start(); 
session_start();

// 2. Aktifkan error reporting agar jika ada masalah, akan muncul pesan error
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

if (isset($_POST['register'])) {
    // Ambil data
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $jurusan      = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $angkatan     = mysqli_real_escape_string($koneksi, $_POST['angkatan']);
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password     = $_POST['password'];

    // 1. Cek apakah username sudah ada
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username '$username' sudah terdaftar! Gunakan nama lain.";
    } else {
        // 2. Simpan ke tabel alumni
        $query_alumni = "INSERT INTO alumni (nama, angkatan, jurusan) VALUES ('$nama_lengkap', '$angkatan', '$jurusan')";
        
        if (mysqli_query($koneksi, $query_alumni)) {
            // Ambil ID Alumni yang baru saja dibuat
            $id_alumni_baru = mysqli_insert_id($koneksi);

            // 3. Simpan ke tabel users
            $query_user = "INSERT INTO users (username, password, role, id_alumni) 
                           VALUES ('$username', '$password', 'user', '$id_alumni_baru')";
            
            if (mysqli_query($koneksi, $query_user)) {
                $id_user_baru = mysqli_insert_id($koneksi);

                // Set Session
                $_SESSION['login']     = true;
                $_SESSION['user_id']   = $id_user_baru;    
                $_SESSION['id_alumni'] = $id_alumni_baru; 
                $_SESSION['username']  = $username;
                $_SESSION['nama']      = $nama_lengkap;
                $_SESSION['role']      = 'user';

                // Redirect menggunakan Script JS agar lebih pasti (Anti-Stuck)
                echo "<script>
                        alert('Registrasi Berhasil! Selamat datang.');
                        window.location.href='dashboard_user.php';
                      </script>";
                exit;
            } else {
                $error = "Gagal menyimpan data user: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Gagal menyimpan data alumni: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Alumni | Sistem Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .bg-red-gradient {
            background: radial-gradient(circle at top right, #dc2626, #991b1b, #7f1d1d);
        }
    </style>
</head>
<body class="bg-red-gradient flex items-center justify-center min-h-screen p-6 antialiased">

    <div class="bg-white/95 backdrop-blur-xl p-10 rounded-[2.5rem] shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] w-full max-w-lg border border-white/20 relative overflow-hidden my-10">
        
        <div class="absolute top-0 left-0 right-0 h-2 bg-red-600"></div>

        <div class="flex flex-col items-center mb-8">
            <div class="bg-red-50 p-4 rounded-3xl mb-4 shadow-sm border border-red-100">
                <img src="logo.png" alt="Logo" class="h-16 w-auto object-contain">
            </div>
            <h2 class="text-3xl font-[800] text-[#1e293b] tracking-tighter uppercase">
                Daftar <span class="text-red-600 italic">Alumni</span>
            </h2>
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.3em] mt-2">Create New Account</p>
        </div>

        <?php if (isset($error)) { ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <?= $error ?>
            </div>
        <?php } ?>

        <form method="POST" class="space-y-5">
            <div class="relative group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-red-600 transition-colors">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" required placeholder="Contoh: Muhammad Evander"
                    class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700">
            </div>

            <div class="relative group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-red-600 transition-colors">Program Keahlian</label>
                <select name="jurusan" required class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700 appearance-none cursor-pointer">
                    <option value="" disabled selected>Pilih Jurusan</option>
                    <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                    <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
                    <option value="Multimedia">Multimedia</option>
                </select>
            </div>

            <div class="relative group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-red-600 transition-colors">Tahun Lulus</label>
                <input type="number" name="angkatan" required placeholder="Contoh: 2026"
                    class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="relative group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-red-600 transition-colors">Username</label>
                    <input type="text" name="username" required placeholder="username"
                        class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700">
                </div>
                <div class="relative group">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1 group-focus-within:text-red-600 transition-colors">Password</label>
                    <input type="password" name="password" required placeholder="••••••"
                        class="w-full px-5 py-3.5 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" name="register"
                    class="w-full bg-[#1e293b] text-white font-[800] py-4 rounded-2xl hover:bg-black shadow-xl transform active:scale-[0.98] transition-all uppercase text-xs tracking-[0.2em]">
                    Daftar & Masuk
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">
                Sudah punya akun? 
                <a href="login.php" class="text-red-600 hover:text-red-800 transition-colors ml-1 border-b-2 border-red-600 pb-0.5">Log In Di Sini</a>
            </p>
        </div>
    </div>

    <div class="fixed bottom-6 text-center w-full hidden md:block">
        <p class="text-white/40 text-[9px] font-black uppercase tracking-[0.5em] italic">System Registration Node &bull; 2026</p>
    </div>

</body>
</html>