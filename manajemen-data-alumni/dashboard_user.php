<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
include 'koneksi.php';

// Jika belum login, tendang ke login.php
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Ambil data session untuk greeting di navbar
$id_saya    = $_SESSION['id_alumni'] ?? null;
$nama_saya  = $_SESSION['nama'] ?? "User"; 

// Logika Pencarian
$keyword = "";
$query = "SELECT * FROM alumni";
if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $query .= " WHERE nama LIKE '%$keyword%' OR angkatan LIKE '%$keyword%' OR jurusan LIKE '%$keyword%'";
}
$query .= " ORDER BY nama ASC";

$data = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Alumni System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,700;0,800;1,400;1,700&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }

        .bg-custom {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url('background.jpg');
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

        .row-hover:hover {
            background-color: rgba(248, 250, 252, 0.8);
            transform: scale(1.002);
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="bg-custom antialiased border-accent">

    <nav class="bg-[#0f172a]/95 backdrop-blur-lg border-b border-white/10 sticky top-0 z-50">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="logo.png" alt="Logo Telkom" class="h-10 w-auto object-contain">
                
                <div class="w-[1px] h-8 bg-white/20 mx-1"></div>

                <div class="bg-red-600 p-2 rounded-xl shadow-lg shadow-red-600/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-white font-extrabold text-2xl tracking-tighter uppercase">
                    USER <span class="text-red-600">PANEL</span>
                </h2>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-gray-400 text-[10px] uppercase font-bold tracking-widest leading-none">Logged in as</p>
                    <p class="text-white font-bold italic text-sm"><?= htmlspecialchars($nama_saya); ?></p>
                </div>
                <a href="logout.php" class="bg-red-600 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase hover:bg-red-700 transition-all shadow-lg shadow-red-600/20 active:scale-95">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-12">
        
        <div class="bg-white rounded-[2.5rem] p-10 mb-12 shadow-2xl flex flex-col md:flex-row justify-between items-center relative overflow-hidden">
            <div class="absolute left-0 top-0 bottom-0 w-4 bg-red-600"></div>
            
            <div class="mb-8 md:mb-0">
                <h1 class="text-5xl font-extrabold text-[#1e293b] tracking-tight">
                    Manajemen <span class="text-red-600 italic">Sistem</span>
                </h1>
                <p class="text-gray-400 text-base mt-2 font-medium">Panel kontrol utama pengelolaan database alumni.</p>
            </div>
            
            <div class="w-full md:w-auto">
                <form action="" method="GET" class="flex items-center bg-gray-50 rounded-2xl border-2 border-gray-100 overflow-hidden md:w-[450px] focus-within:border-red-500 transition-all">
                    <input type="text" name="cari" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari nama, angkatan, atau jurusan..." 
                        class="flex-1 px-6 py-5 bg-transparent outline-none text-sm font-semibold text-gray-700">
                    <button type="submit" class="bg-red-600 text-white px-10 py-5 text-xs font-black uppercase hover:bg-red-700 transition-all active:bg-red-800">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <div class="mb-6 flex items-center gap-4 px-4">
             <span class="flex-none text-white/40 text-xs font-black uppercase tracking-[0.4em]">Database Alumni</span>
             <div class="h-[1px] w-full bg-white/10"></div>
        </div>

        <div class="bg-white rounded-[3rem] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.4)] border border-white/20">
            <table class="w-full text-left border-collapse">
                <thead class="bg-[#1e293b] text-white">
                    <tr>
                        <th class="p-8 text-[11px] uppercase font-black tracking-[0.2em]">Nama Lengkap</th>
                        <th class="p-8 text-[11px] uppercase font-black tracking-[0.2em] text-center">Angkatan</th>
                        <th class="p-8 text-[11px] uppercase font-black tracking-[0.2em]">Program Studi / Jurusan</th>
                        <th class="p-8 text-[11px] uppercase font-black tracking-[0.2em] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): 
                            $is_my_data = ($id_saya && $row['id_alumni'] == $id_saya);
                            $initial = strtoupper(substr($row['nama'], 0, 1));
                            $foto_profil = $row['foto']; 
                            $path_foto = 'uploads/' . $foto_profil;
                        ?>
                        <tr class="row-hover transition-all">
                            <td class="p-8">
                                <div class="flex items-center gap-5">
                                    <div class="relative">
                                        <?php if (!empty($foto_profil) && file_exists($path_foto)): ?>
                                            <div class="h-14 w-14 rounded-full p-1 border-2 <?= $is_my_data ? 'border-red-600' : 'border-gray-200' ?> shadow-inner overflow-hidden">
                                                <img src="<?= $path_foto ?>" class="h-full w-full rounded-full object-cover" alt="Avatar">
                                            </div>
                                        <?php else: ?>
                                            <div class="h-14 w-14 rounded-full <?= $is_my_data ? 'bg-red-600' : 'bg-[#1e293b]' ?> text-white flex items-center justify-center font-black text-lg shadow-lg border-4 border-white">
                                                <?= $initial; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if($is_my_data): ?>
                                            <div class="absolute -bottom-1 -right-1 bg-green-500 h-4 w-4 rounded-full border-2 border-white"></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <span class="font-extrabold italic text-lg tracking-tight <?= $is_my_data ? 'text-red-600' : 'text-[#1e293b]'; ?>">
                                        <?= htmlspecialchars($row['nama']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="p-8 text-center">
                                <span class="bg-[#1e293b] text-white text-[12px] font-black px-5 py-2 rounded-xl shadow-md">
                                    <?= htmlspecialchars($row['angkatan']); ?>
                                </span>
                            </td>
                            <td class="p-8">
                                <p class="text-sm italic font-bold text-gray-500 tracking-wide uppercase">
                                    <?= htmlspecialchars($row['jurusan']); ?>
                                </p>
                            </td>
                            <td class="p-8 text-center">
                                <?php if($is_my_data): ?>
                                    <a href="edit.php?id=<?= $row['id_alumni']; ?>" class="inline-block bg-red-600 text-white px-8 py-2.5 rounded-xl text-[11px] font-black uppercase hover:bg-red-700 transition-all shadow-lg shadow-red-600/30 hover:-translate-y-0.5">
                                        Edit Profil
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-300 text-[10px] font-black uppercase italic tracking-widest opacity-60">Hanya Lihat</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="p-32 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="text-gray-200 mb-4">
                                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <p class="text-gray-400 italic text-lg font-medium">Data alumni tidak ditemukan dalam database.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer class="mt-20 text-center">
            <div class="inline-block glass px-8 py-4 rounded-2xl mb-4">
                <p class="text-[11px] text-white/50 uppercase tracking-[0.6em] font-black">
                    Administrator Control Panel • 2026
                </p>
            </div>
            <p class="text-sm text-white/60 font-medium italic">
                Developed with passion by <span class="text-red-500 font-black not-italic">Muhammad Evander Alvaro</span>
            </p>
        </footer>
    </main>
</body>
</html>