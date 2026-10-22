<?php
session_start();

$host = 'localhost';
$db   = 'db_santri';
$user = 'root';
$pass = 'tkjtkj';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

// Fungsi bantu: mengamankan teks sebelum ditampilkan di HTML
function e($teks)
{
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

