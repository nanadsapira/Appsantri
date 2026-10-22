
<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');
$kamar = trim($_GET['kamar'] ?? '');

$sql = 'SELECT * FROM santri WHERE 1=1';
$params = [];

if ($cari !== '') {
    $sql .= ' AND nama LIKE :cari';
    $params['cari'] = "%$cari%";
}

if ($kamar !== '') {
    $sql .= ' AND kamar = :kamar';
    $params['kamar'] = $kamar;
}

$sql .= ' ORDER BY id DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="daftar_santri.csv"');

$output = fopen('php://output', 'w');

// BOM agar teks UTF-8 lebih mudah dibaca Excel
fwrite($output, "\xEF\xBB\xBF");

fputcsv($output, [
    'No',
    'Nama',
    'Jenis Kelamin',
    'Tanggal Lahir',
    'Umur',
    'Asal Daerah',
    'Kamar',
    'No HP Wali',
    'Angkatan'
]);

$no = 1;

while ($s = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $umur = '';

    if (!empty($s['tanggal_lahir'])) {
        $tanggalLahir = new DateTime($s['tanggal_lahir']);
        $hariIni = new DateTime();
        $umur = $hariIni->diff($tanggalLahir)->y;
    }

    fputcsv($output, [
        $no++,
        $s['nama'],
        $s['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
        $s['tanggal_lahir'],
        $umur,
        $s['asal_daerah'],
        $s['kamar'],
        $s['no_hp_wali'],
        $s['angkatan']
    ]);
}

fclose($output);
exit;
