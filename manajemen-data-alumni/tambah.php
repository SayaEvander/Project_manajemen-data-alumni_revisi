<?php
// Cek status session agar tidak error "session already active"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role_saya = $_SESSION['role'];
$id_saya = $_SESSION['id_alumni'] ?? null;
$nama_saya = $_SESSION['nama'] ?? "User";

// --- CEK APAKAH USER SUDAH PUNYA DATA ---
if ($role_saya == 'user' && $id_saya) {
    $cek_data = mysqli_query($koneksi, "SELECT id_alumni FROM alumni WHERE id_alumni = '$id_saya'");
    if (mysqli_num_rows($cek_data) > 0) {
        header("Location: dashboard_user.php");
        exit;
    }
}

// Proses Tambah Data
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $angkatan = mysqli_real_escape_string($koneksi, $_POST['angkatan']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);

    if ($role_saya == 'user') {
        $query = "INSERT INTO alumni (id_alumni, nama, angkatan, jurusan) VALUES ('$id_saya', '$nama', '$angkatan', '$jurusan')";
    } else {
        $query = "INSERT INTO alumni (nama, angkatan, jurusan) VALUES ('$nama', '$angkatan', '$jurusan')";
    }
    
    if (mysqli_query($koneksi, $query)) {
        if ($role_saya == 'admin' || $role_saya == 'superadmin') {
            header("Location: dashboard_admin.php");
        } else {
            header("Location: dashboard_user.php");
        }
        exit;
    } else {
        $error = "Gagal menyimpan data! Pastikan data belum ada.";
    }
}

$judul_halaman = ($role_saya == 'user') ? "Lengkapi Profil" : "Tambah Alumni";
$sub_judul = ($role_saya == 'user') ? "Isi data profil Anda untuk sistem alumni." : "Tambahkan data alumni baru ke dalam sistem.";
$link_batal = ($role_saya == 'user') ? "dashboard_user.php" : "dashboard_admin.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_halaman ?> | Alumni System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,700;0,800;1,400;1,700&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }

        .bg-custom {
            background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), url('background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .border-accent {
            border-top: 6px solid #e11d48;
            border-bottom: 6px solid #e11d48;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-custom antialiased border-accent">

    <nav class="bg-[#0f172a]/95 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="bg-red-600 p-2 rounded-xl shadow-lg shadow-red-600/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <h2 class="text-white font-extrabold text-2xl tracking-tighter uppercase">
                    <?= strtoupper($role_saya) ?> <span class="text-red-600">PANEL</span>
                </h2>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest leading-none">Logged in as</p>
                    <p class="text-white font-bold italic text-sm"><?= htmlspecialchars($nama_saya); ?></p>
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-12 flex justify-center">
        <div class="bg-white rounded-[2.5rem] p-10 shadow-2xl w-full max-w-lg relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-3 bg-red-600"></div>
            
            <div class="mb-10">
                <h1 class="text-4xl font-extrabold text-[#1e293b] tracking-tight">
                    <?= ($role_saya == 'user') ? 'Lengkapi <span class="text-red-600 italic">Profil</span>' : 'Tambah <span class="text-red-600 italic">Data</span>' ?>
                </h1>
                <p class="text-gray-400 text-sm mt-2 font-medium"><?= $sub_judul ?></p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 rounded-xl mb-6 text-xs font-bold">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama Lengkap</label>
                    <input type="text" name="nama" required 
                        placeholder="Nama sesuai ijazah..."
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:outline-none focus:border-red-500 transition font-bold text-gray-700">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Angkatan</label>
                    <input type="number" name="angkatan" required 
                        placeholder="Tahun lulus (Contoh: 2024)"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:outline-none focus:border-red-500 transition font-bold text-gray-700">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Program Studi</label>
                    <div class="relative">
                        <select name="jurusan" required 
                            class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:outline-none focus:border-red-500 transition font-bold text-gray-700 appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih Jurusan</option>
                            <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                            <option value="Teknik Komputer dan Jaringan">Teknik Komputer dan Jaringan</option>
                            <option value="Teknik Jaringan Akses Telekomunikasi">Teknik Jaringan Akses Telekomunikasi</option>
                            <option value="Animasi">Animasi</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-6">
                    <button type="submit" name="submit"
                        class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-5 rounded-2xl shadow-lg shadow-red-600/30 transition-all active:scale-95 uppercase tracking-widest text-xs">
                        Simpan Data Alumni
                    </button>

                    <a href="<?= $link_batal ?>"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-500 font-black py-4 rounded-2xl text-center transition-all text-xs uppercase tracking-widest">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="mt-10 mb-20 text-center">
        <div class="inline-block glass px-8 py-4 rounded-2xl mb-4">
            <p class="text-[11px] text-white/50 uppercase tracking-[0.6em] font-black">
                Administrator Control Panel • 2026
            </p>
        </div>
        <p class="text-sm text-white/60 font-medium italic">
            Developed with passion by <span class="text-red-500 font-black not-italic">Muhammad Evander Alvaro</span>
        </p>
    </footer>
</body>
</html>