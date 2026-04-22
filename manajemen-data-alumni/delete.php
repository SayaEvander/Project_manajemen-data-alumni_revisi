<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Cek role (hanya admin)
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin') {
    echo "Akses ditolak!";
    exit;
}

// Cek apakah id ada
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID tidak valid!";
    exit;
}

$id = $_GET['id'];

// Hapus data
mysqli_query($koneksi, "DELETE FROM alumni WHERE id_alumni='$id'");

// Kembali ke dashboard
header("Location: dashboard_admin.php");
exit;
?>