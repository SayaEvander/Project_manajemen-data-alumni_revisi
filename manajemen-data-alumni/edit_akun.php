<?php
session_start();
include 'koneksi.php';

// 1. PROTEKSI HALAMAN (Hanya Superadmin yang boleh edit akun)
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'superadmin') {
    echo "<script>alert('Akses Ditolak! Hanya Superadmin yang dapat mengakses halaman ini.'); window.location='dashboard_admin.php';</script>";
    exit;
}

// 2. AMBIL ID DARI URL (user_id)
if (!isset($_GET['id'])) {
    header("Location: dashboard_admin.php");
    exit;
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// 3. AMBIL DATA USER BERDASARKAN user_id
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE user_id = '$id'");
$user_data = mysqli_fetch_assoc($query);

// Jika user tidak ditemukan
if (!$user_data) {
    header("Location: dashboard_admin.php");
    exit;
}

// 4. PROSES UPDATE DATA
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password_baru = $_POST['password']; // Ambil input password

    // Cek apakah password diisi atau ingin diganti
    if (!empty($password_baru)) {
        // Jika diisi, enkripsi password baru
        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET 
                username = '$username', 
                role = '$role',
                password = '$hashed_password' 
                WHERE user_id = '$id'";
    } else {
        // Jika kosong, update tanpa mengubah password lama
        $sql = "UPDATE users SET 
                username = '$username', 
                role = '$role' 
                WHERE user_id = '$id'";
    }

    $update = mysqli_query($koneksi, $sql);

    if ($update) {
        echo "<script>alert('Akun berhasil diperbarui!'); window.location='dashboard_admin.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui akun: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun Pengguna | Sistem Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 flex flex-col min-h-screen font-sans">

    <nav class="bg-gray-900 border-b-4 border-red-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="bg-red-600 p-2 rounded-lg mr-3 shadow-lg shadow-red-900/50">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="text-white text-xl font-black tracking-tighter uppercase">
                        Account <span class="text-red-500">Editor</span>
                    </span>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-400 text-xs font-bold uppercase tracking-widest italic hidden md:block">Priviledge: Superadmin Mode</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center p-6">
        <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-lg border border-gray-200 relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-2 bg-red-600"></div>

            <div class="mb-10 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-50 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-gray-800 tracking-tight">
                    Edit <span class="text-red-600">Akun</span>
                </h2>
                <p class="text-sm text-gray-500 font-medium mt-2 italic">Mengubah kredensial akses pengguna sistem.</p>
            </div>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Username Pengguna</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user_data['username']); ?>" required
                        class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-600 transition bg-gray-50 font-bold text-gray-700 italic">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ingin diganti"
                        class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-600 transition bg-gray-50 font-bold text-gray-700">
                    <p class="text-[9px] text-red-400 mt-2 ml-1">*Isi kolom ini jika alumni lupa password mereka.</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 ml-1">Otoritas / Hak Akses</label>
                    <div class="relative">
                        <select name="role" required 
                            class="w-full px-5 py-4 border-2 border-gray-100 rounded-2xl focus:outline-none focus:ring-4 focus:ring-red-500/10 focus:border-red-600 transition bg-gray-50 font-bold text-gray-700 appearance-none cursor-pointer">
                            <option value="user" <?= ($user_data['role'] == 'user') ? 'selected' : ''; ?>>User (Alumni)</option>
                            <option value="admin" <?= ($user_data['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                            <option value="superadmin" <?= ($user_data['role'] == 'superadmin') ? 'selected' : ''; ?>>Superadmin</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 7.293 8.172 5.879 9.586z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-6">
                    <button type="submit" name="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-red-200 transition-all active:scale-95 uppercase tracking-widest text-sm">
                        Simpan Perubahan
                    </button>

                    <a href="dashboard_admin.php"
                        class="w-full bg-gray-900 hover:bg-black text-white font-bold py-4 rounded-2xl text-center transition-all text-sm uppercase tracking-widest shadow-lg">
                        Kembali ke Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-gray-900 py-10 border-t-8 border-red-600">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-500 text-[10px] font-bold uppercase tracking-[0.5em]">Administrator Control Panel &bull; 2026</p>
            <p class="text-white mt-2 font-light text-sm italic">Developed by <span class="text-red-500 font-bold not-italic">Muhammad Evander Alvaro</span></p>
        </div>
    </footer>

</body>
</html>