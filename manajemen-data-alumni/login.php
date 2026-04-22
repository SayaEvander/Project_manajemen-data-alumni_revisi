<?php
ob_start(); 
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        if ($password == $user['password']) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama'] = $user['username'];

            if (isset($user['id_alumni'])) {
                $_SESSION['id_alumni'] = $user['id_alumni'];
            } else {
                $cek_alumni = mysqli_query($koneksi, "SELECT id_alumni FROM alumni WHERE nama='$username' LIMIT 1");
                if ($cek_alumni && mysqli_num_rows($cek_alumni) > 0) {
                    $data_alumni = mysqli_fetch_assoc($cek_alumni);
                    $_SESSION['id_alumni'] = $data_alumni['id_alumni'];
                } else {
                    $_SESSION['id_alumni'] = null;
                }
            }

            if ($user['role'] == 'admin' || $user['role'] == 'superadmin') {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit; 
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Alumni</title>
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

    <div class="bg-white/95 backdrop-blur-xl p-10 rounded-[2.5rem] shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] w-full max-w-md border border-white/20 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 right-0 h-2 bg-red-600"></div>

        <div class="flex flex-col items-center mb-10">
            <div class="bg-red-50 p-4 rounded-3xl mb-4 shadow-sm border border-red-100">
                <img src="./logo.png" alt="Logo" class="h-20 w-auto object-contain">
            </div>
            
            <h2 class="text-3xl font-[800] text-[#1e293b] tracking-tighter uppercase">
                Panel <span class="text-red-600 italic">Alumni</span>
            </h2>
            <p class="text-gray-400 font-bold text-[10px] uppercase tracking-[0.3em] mt-2">Authentication Gateway</p>
        </div>

        <?php if (isset($error)) : ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-8 text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <?= $error ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div class="relative group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-red-600 transition-colors">Account Username</label>
                <input type="text" name="username" required placeholder="Masukkan username"
                    class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700">
            </div>

            <div class="relative group">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1 group-focus-within:text-red-600 transition-colors">Access Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-red-500/10 focus:border-red-600 outline-none transition-all font-bold text-gray-700">
            </div>

            <button type="submit" name="login"
                class="w-full bg-red-600 text-white font-[800] py-4 rounded-2xl hover:bg-red-700 shadow-lg shadow-red-600/30 transform active:scale-[0.98] transition-all uppercase text-xs tracking-[0.2em]">
                Log In System
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">
                Belum terdaftar? 
                <a href="register.php" class="text-red-600 hover:text-red-800 transition-colors ml-1 border-b-2 border-red-600 pb-0.5">Daftar Sekarang</a>
            </p>
        </div>
    </div>

    <div class="fixed bottom-6 text-center w-full">
        <p class="text-white/40 text-[9px] font-black uppercase tracking-[0.5em] italic">Authorized Access Only &bull; 2026</p>
    </div>

</body>
</html>