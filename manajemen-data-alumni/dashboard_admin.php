<?php
// --- SETUP & DEBUGGING ---
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// 2. CEK ROLE
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin') {
    echo "<script>alert('Akses Ditolak! Anda bukan admin.'); window.location='dashboard_user.php';</script>";
    exit;
}

$role_user      = $_SESSION['role'];
$username_login = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Admin'; 
$id_login       = $_SESSION['id_alumni'] ?? 0; // ID alumni dari session jika admin juga alumni

// 3. LOGIKA PENCARIAN ALUMNI
$keyword = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['search']);
    $query_alumni = "SELECT * FROM alumni WHERE 
                     nama LIKE '%$keyword%' OR 
                     angkatan LIKE '%$keyword%' OR 
                     jurusan LIKE '%$keyword%' 
                     ORDER BY nama ASC";
} else {
    $query_alumni = "SELECT * FROM alumni ORDER BY nama ASC";
}
$data_alumni = mysqli_query($koneksi, $query_alumni);

// 4. LOGIKA DATA AKUN (Khusus Superadmin)
$data_akun = null;
if ($role_user == 'superadmin') {
    $data_akun = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Sistem Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,700;0,800;1,400;1,700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        .bg-pro-dark {
            background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), url('background.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .row-hover:hover {
            background-color: rgba(248, 250, 252, 0.05);
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="bg-pro-dark flex flex-col min-h-screen antialiased">

    <nav class="bg-gray-900/50 backdrop-blur-md border-b-4 border-red-600 sticky top-0 z-50 shadow-2xl">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-4">
                    <img src="logo.png" alt="Logo Telkom" class="h-10 w-auto object-contain">
                    
                    <div class="w-[1px] h-8 bg-white/20 mx-1"></div>

                    <div class="bg-red-600 p-2 rounded-xl shadow-lg shadow-red-900/40">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                    </div>
                    <span class="text-white text-2xl font-black tracking-tighter uppercase">
                        <?= htmlspecialchars($role_user); ?> <span class="text-red-600">PANEL</span>
                    </span>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-gray-500 text-[10px] uppercase font-black tracking-widest leading-none">Accessing as</p>
                        <p class="text-white font-bold italic text-sm"><?= htmlspecialchars($username_login); ?></p>
                    </div>
                    <a href="logout.php" class="bg-red-600 text-white px-6 py-2.5 rounded-xl font-black hover:bg-red-700 transition-all text-xs uppercase shadow-lg shadow-red-600/20 active:scale-95">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-grow container mx-auto px-6 py-12">
        
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-10 relative overflow-hidden mb-12 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="absolute left-0 top-0 bottom-0 w-4 bg-red-600"></div>
            <div>
                <h1 class="text-4xl font-extrabold text-[#1e293b] tracking-tight">Manajemen <span class="text-red-600 italic">Sistem</span></h1>
                <p class="text-gray-400 mt-2 font-medium">Otoritas penuh pengelolaan database alumni dan akses pengguna.</p>
            </div>
            
            <form action="" method="GET" class="flex w-full md:w-auto shadow-sm">
                <input type="text" name="search" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari data alumni..." 
                       class="w-full md:w-80 px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-l-2xl focus:outline-none focus:border-red-600 transition-all font-semibold text-gray-700">
                <button type="submit" class="bg-red-600 text-white px-8 py-4 rounded-r-2xl hover:bg-red-700 font-black uppercase text-xs transition-all">Cari</button>
            </form>
        </div>

        <?php if ($role_user == 'superadmin' && $data_akun): ?>
        <div class="mb-16">
            <div class="flex items-center mb-8 px-4">
                <h2 class="text-white/40 text-xs font-black uppercase tracking-[0.4em]">
                    <span class="text-red-600">01.</span> Account Authority
                </h2>
                <div class="flex-grow h-[1px] bg-white/10 ml-6"></div>
            </div>
            
            <div class="bg-white/5 backdrop-blur-sm shadow-2xl rounded-[2rem] overflow-hidden border border-white/10">
                <table class="min-w-full">
                    <thead class="bg-gray-800/80 text-white text-[10px] uppercase font-black tracking-widest">
                        <tr>
                            <th class="py-5 px-10 text-left">Username Akun</th>
                            <th class="py-5 px-8 text-center">Level Akses</th>
                            <th class="py-5 px-10 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php while ($akun = mysqli_fetch_assoc($data_akun)) : 
                            $is_me_acc = ($akun['username'] == $_SESSION['username']);
                        ?>
                        <tr class="row-hover">
                            <td class="py-5 px-10">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-200"><?= htmlspecialchars($akun['username']); ?></span>
                                    <?php if($is_me_acc): ?>
                                        <span class="text-[9px] bg-red-600 text-white px-2 py-0.5 rounded-md font-black">MY ACCOUNT</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-5 px-8 text-center">
                                <span class="<?= $akun['role'] == 'superadmin' ? 'bg-red-600/20 text-red-400 border-red-600/30' : 'bg-blue-600/20 text-blue-400 border-blue-600/30'; ?> px-3 py-1 rounded-lg text-[10px] font-black uppercase border italic">
                                    <?= htmlspecialchars($akun['role']); ?>
                                </span>
                            </td>
                            <td class="py-5 px-10 text-center">
                                <div class="flex justify-center items-center gap-6">
                                    <a href="edit_akun.php?id=<?= $akun['user_id']; ?>" class="text-blue-400 hover:text-white font-bold text-[10px] uppercase transition-colors tracking-widest">Edit</a>
                                    <?php if(!$is_me_acc): ?>
                                        <a href="hapus_akun.php?id=<?= $akun['user_id']; ?>" onclick="return confirm('Hapus akses akun ini?')" class="text-red-500 hover:text-white font-bold text-[10px] uppercase transition-colors tracking-widest">Remove</a>
                                    <?php else: ?>
                                        <span class="text-white/20 text-[10px] font-black uppercase italic">Master</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <div>
            <div class="flex items-center mb-8 px-4">
                <h2 class="text-white/40 text-xs font-black uppercase tracking-[0.4em]">
                    <span class="text-red-600"><?= ($role_user == 'superadmin') ? '02.' : '01.'; ?></span> Database Alumni
                </h2>
                <div class="flex-grow h-[1px] bg-white/10 ml-6"></div>
                <a href="tambah.php" class="ml-6 bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-all text-[10px] font-black uppercase shadow-lg shadow-red-600/20 flex items-center gap-2">
                    <span class="text-lg leading-none">+</span> Tambah Data
                </a>
            </div>

            <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-[#1e293b] text-white text-[10px] uppercase font-black tracking-widest">
                            <tr>
                                <th class="py-6 px-10 text-left">Nama Lengkap</th>
                                <th class="py-6 px-8 text-center">Angkatan</th>
                                <th class="py-6 px-10 text-left">Program Studi</th>
                                <th class="py-6 px-10 text-center">Opsi Kendali</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (mysqli_num_rows($data_alumni) > 0) : ?>
                                <?php while ($row = mysqli_fetch_assoc($data_alumni)) : 
                                    $is_me = ($id_login && $row['id_alumni'] == $id_login);
                                    $initial = strtoupper(substr($row['nama'], 0, 1));
                                    $path_foto = 'uploads/' . ($row['foto'] ?? '');
                                ?>
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="py-6 px-10">
                                        <div class="flex items-center gap-4">
                                            <?php if (!empty($row['foto']) && file_exists($path_foto)): ?>
                                                <div class="h-12 w-12 rounded-full p-0.5 border-2 <?= $is_me ? 'border-red-600' : 'border-gray-200'; ?> shadow-sm">
                                                    <img src="<?= $path_foto ?>" class="h-full w-full rounded-full object-cover" alt="User">
                                                </div>
                                            <?php else: ?>
                                                <div class="h-12 w-12 rounded-full <?= $is_me ? 'bg-red-600' : 'bg-[#1e293b]'; ?> text-white flex items-center justify-center font-black text-xs shadow-md border-2 border-white ring-1 ring-gray-100">
                                                    <?= $initial; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="font-extrabold italic text-base tracking-tight <?= $is_me ? 'text-red-600' : 'text-[#1e293b]'; ?>">
                                                <?= htmlspecialchars($row['nama']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6 px-8 text-center">
                                        <span class="bg-[#1e293b] text-white px-4 py-1.5 rounded-lg text-[11px] font-black shadow-sm">
                                            <?= htmlspecialchars($row['angkatan']); ?>
                                        </span>
                                    </td>
                                    <td class="py-6 px-10 text-gray-400 font-bold italic text-sm uppercase"><?= htmlspecialchars($row['jurusan']); ?></td>
                                    <td class="py-6 px-10 text-center">
                                        <div class="flex justify-center items-center gap-6">
                                            <a href="edit.php?id=<?= $row['id_alumni']; ?>" class="text-amber-500 hover:text-amber-700 font-black text-[10px] uppercase tracking-widest transition-colors">Modify</a>
                                            <a href="delete.php?id=<?= $row['id_alumni']; ?>" onclick="return confirm('Hapus permanen data ini?')" class="text-red-600 hover:text-red-800 font-black text-[10px] uppercase tracking-widest transition-colors">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" class="py-24 text-center">
                                        <p class="text-gray-400 font-bold italic text-base uppercase tracking-widest">-- Data Tidak Ditemukan --</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-900/80 py-12 border-t-8 border-red-600 mt-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-gray-600 text-[10px] font-black uppercase tracking-[0.6em] mb-4">Centralized Administrator Control Panel &bull; 2026</p>
            <p class="text-white/60 font-medium text-sm italic">
                Authorized System Developed by <span class="text-red-600 font-black not-italic">Muhammad Evander Alvaro</span>
            </p>
        </div>
    </footer>

</body>
</html>