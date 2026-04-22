<?php
session_start();
include 'koneksi.php';

// 1. PROTEKSI HALAMAN
// Pastikan user sudah login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// 2. CEK ROLE
// Hanya Superadmin yang punya hak akses ke file ini
if ($_SESSION['role'] != 'superadmin') {
    echo "<script>
            alert('Akses Ditolak! Hanya Superadmin yang boleh menghapus akun.'); 
            window.location='dashboard_admin.php';
          </script>";
    exit;
}

// 3. PROSES HAPUS AKUN
if (isset($_GET['id'])) {
    // Mengambil ID dari parameter URL
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    /** * PENTING: Pastikan kunci session ini ($_SESSION['user_id']) 
     * sama dengan yang Anda buat di login.php agar fitur 'Anti Hapus Diri Sendiri' bekerja.
     */
    $id_login = $_SESSION['user_id'] ?? ''; 

    // Validasi: Mencegah menghapus akun sendiri
    if ($id_hapus == $id_login) {
        echo "<script>
                alert('Tindakan Ditolak! Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif.'); 
                window.location='dashboard_admin.php';
              </script>";
        exit;
    }

    // Eksekusi Query Hapus
    // Pastikan nama kolom 'user_id' sesuai dengan yang ada di tabel database Anda
    $query = "DELETE FROM users WHERE user_id = '$id_hapus'";
    $eksekusi = mysqli_query($koneksi, $query);

    if ($eksekusi) {
        echo "<script>
                alert('Sukses! Akun pengguna telah dihapus dari sistem.'); 
                window.location='dashboard_admin.php';
              </script>";
    } else {
        // Jika gagal, tampilkan pesan error dari database untuk memudahkan debug
        $error = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus akun: $error'); 
                window.location='dashboard_admin.php';
              </script>";
    }
} else {
    // Jika mencoba akses file tanpa ID melalui URL
    header("Location: dashboard_admin.php");
    exit;
}
?>