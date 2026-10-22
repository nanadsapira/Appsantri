<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);

    $stmt = $pdo->prepare('DELETE FROM santri WHERE id = ?');
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['pesan'] = 'Data santri berhasil dihapus.';
    } else {
        $_SESSION['pesan'] = 'Data santri tidak ditemukan.';
        $_SESSION['tipe']  = 'danger';
    }
}

header('Location: index.php');
exit;

