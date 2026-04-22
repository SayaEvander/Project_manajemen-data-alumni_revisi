<?php
// --- SETUP & DEBUGGING ---
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'koneksi.php';

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }

// 2. VALIDASI ID
if (!isset($_GET['id'])) { 
    $redirect = ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'superadmin') ? 'dashboard_admin.php' : 'dashboard_user.php';
    header("Location: $redirect"); 
    exit; 
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$id_saya = $_SESSION['id_alumni'] ?? null;
$role_saya = $_SESSION['role'];

// 3. PROTEKSI AKSES (Hanya Admin atau Pemilik Data yang bisa edit)
if (($role_saya != 'admin' && $role_saya != 'superadmin') && $id != $id_saya) {
    echo "<script>alert('Akses Ditolak!'); window.location='dashboard_user.php';</script>"; exit;
}

// 4. AMBIL DATA LAMA
$data = mysqli_query($koneksi, "SELECT * FROM alumni WHERE id_alumni='$id'");
$d = mysqli_fetch_assoc($data);

if (!$d) { echo "Data tidak ditemukan!"; exit; }

// 5. LOGIKA UPDATE DATA (Hanya Teks)
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $angkatan = mysqli_real_escape_string($koneksi, $_POST['angkatan']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    
    // Query yang jauh lebih sederhana tanpa foto
    $query = "UPDATE alumni SET 
              nama = '$nama', 
              angkatan = '$angkatan', 
              jurusan = '$jurusan' 
              WHERE id_alumni = '$id'";

    if (mysqli_query($koneksi, $query)) {
        $redirect = ($role_saya == 'admin' || $role_saya == 'superadmin') ? 'dashboard_admin.php' : 'dashboard_user.php';
        echo "<script>alert('Data berhasil diperbarui!'); window.location='$redirect';</script>";
        exit;
    } else {
        $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil | Sistem Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-custom {
            background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), url('background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-custom min-h-screen flex items-center justify-center p-6 antialiased">

    <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl w-full max-w-lg border border-white/20 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-2 bg-red-600"></div>

        <div class="mb-8 text-center">
            <h2 class="text-3xl font-black text-[#1e293b]">Edit <span class="text-red-600 italic">Profil</span></h2>
            <p class="text-gray-400 text-sm font-medium mt-1">Perbarui informasi data alumni di bawah ini.</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 text-sm font-bold">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($d['nama']) ?>" 
                       class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all font-bold text-gray-700" required>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Angkatan (Tahun)</label>
                <input type="number" name="angkatan" value="<?= htmlspecialchars($d['angkatan']) ?>" 
                       class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all font-bold text-gray-700" required>
            </div>

            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Program Studi / Jurusan</label>
                <select name="jurusan" class="w-full px-5 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all font-bold text-gray-700 appearance-none">
                    <option value="Rekayasa Perangkat Lunak" <?= $d['jurusan'] == 'Rekayasa Perangkat Lunak' ? 'selected' : '' ?>>Rekayasa Perangkat Lunak</option>
                    <option value="Teknik Komputer dan Jaringan" <?= $d['jurusan'] == 'Teknik Komputer dan Jaringan' ? 'selected' : '' ?>>Teknik Komputer dan Jaringan</option>
                    <option value="Multimedia" <?= $d['jurusan'] == 'Multimedia' ? 'selected' : '' ?>>Multimedia</option>
                    <option value="Sistem Informasi" <?= $d['jurusan'] == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
                </select>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row gap-4">
                <button type="submit" name="submit" 
                        class="flex-1 bg-red-600 text-white font-black py-4 rounded-2xl hover:bg-red-700 transition-all shadow-lg shadow-red-600/30 active:scale-95 uppercase text-xs tracking-widest">
                    Simpan Perubahan
                </button>
                <a href="<?= ($role_saya == 'admin' || $role_saya == 'superadmin') ? 'dashboard_admin.php' : 'dashboard_user.php' ?>" 
                   class="flex-1 bg-gray-100 text-gray-500 text-center py-4 rounded-2xl font-black hover:bg-gray-200 transition-all uppercase text-xs tracking-widest">
                    Batal
                </a>
            </div>
        </form>
    </div>

</body>
</html>