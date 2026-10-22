<?php
require 'koneksi.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM santri WHERE id = ?');
$stmt->execute([$id]);
$santri = $stmt->fetch();
$dataForm = $santri;

if (!$santri) {
  $_SESSION['pesan'] = 'Data santri tidak ditemukan.';
  $_SESSION['tipe']  = 'danger';
  header('Location: index.php');
  exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $kolomForm = ['nama', 'jenis_kelamin', 'tanggal_lahir', 'asal_daerah', 'kamar', 'no_hp_wali'];
  foreach ($kolomForm as $kolom) {
    $santri[$kolom] = trim($_POST[$kolom] ?? '');
  }

  $dataForm = $santri;

  if ($santri['nama'] === '') {
    $errors[] = 'Nama wajib diisi.';
  }
  if (!in_array($santri['jenis_kelamin'], ['L', 'P'])) {
    $errors[] = 'Jenis kelamin wajib dipilih.';
  }

  if (count($errors) === 0) {
    $sql = 'UPDATE santri SET
                  nama          = :nama,
                  jenis_kelamin = :jk,
                  tanggal_lahir = :tgl,
                  asal_daerah   = :asal,
                  kamar         = :kamar,
                  no_hp_wali    = :hp
                WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':nama'  => $santri['nama'],
      ':jk'    => $santri['jenis_kelamin'],
      ':tgl'   => $santri['tanggal_lahir'] ?: null,
      ':asal'  => $santri['asal_daerah'],
      ':kamar' => $santri['kamar'],
      ':hp'    => $santri['no_hp_wali'],
      ':id'    => $id,
    ]);

    $_SESSION['pesan'] = 'Data santri berhasil diperbarui.';
    header('Location: index.php');
    exit;
  }
}

include 'header.php';
?>

<h4 class="mb-3">Edit Santri</h4>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $pesan): ?>
        <li><?= e($pesan) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php include 'form.php'; ?>

<?php include 'footer.php'; ?>