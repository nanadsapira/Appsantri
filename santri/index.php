<?php
require 'koneksi.php';



$cari = trim($_GET['cari'] ?? '');
$kamar = trim($_GET['kamar'] ?? '');

$perHalaman = 10;
$halaman = max(1, (int) ($_GET['halaman'] ?? 1));

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

// Hitung jumlah seluruh data sesuai pencarian dan filter
$sqlHitung = str_replace('SELECT *', 'SELECT COUNT(*)', $sql);
$stmtHitung = $pdo->prepare($sqlHitung);
$stmtHitung->execute($params);
$totalData = (int) $stmtHitung->fetchColumn();

$totalHalaman = max(1, (int) ceil($totalData / $perHalaman));
$halaman = min($halaman, $totalHalaman);
$offset = ($halaman - 1) * $perHalaman;

// Ambil maksimal 10 santri pada halaman aktif
$sql .= ' ORDER BY id DESC LIMIT :limit OFFSET :offset';
$stmt = $pdo->prepare($sql);

foreach ($params as $nama => $nilai) {
  $stmt->bindValue(':' . $nama, $nilai, PDO::PARAM_STR);
}

$stmt->bindValue(':limit', $perHalaman, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$daftar = $stmt->fetchAll(PDO::FETCH_ASSOC);


include 'header.php';
?>


<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Daftar Santri</h4>

  <div class="d-flex gap-2">
    <a href="export.php?<?= e(http_build_query([
        'cari' => $cari,
        'kamar' => $kamar
    ])) ?>" class="btn btn-outline-success">
      Download CSV
    </a>

    <a href="tambah.php" class="btn btn-success">
      + Tambah Santri
    </a>
  </div>
</div>

<form method="get" class="row g-2 mb-3">
  <div class="col-sm-5">
    <input type="text" name="cari"
      class="form-control"
      placeholder="Cari nama santri..."
      value="<?= e($cari) ?>">
  </div>

  <div class="col-sm-3">
    <select name="kamar" class="form-select">
      <option value="">Semua Kamar</option>
      <?php foreach (['A1', 'A2', 'A3', 'B1', 'B2', 'C1'] as $k): ?>
        <option value="<?= e($k) ?>"
          <?= $kamar === $k ? 'selected' : '' ?>>
          <?= e($k) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-auto">
    <button type="submit" class="btn btn-outline-success">Cari</button>
    <a href="index.php" class="btn btn-outline-secondary">Reset</a>
  </div>
</form>


<p class="text-muted">Menampilkan <?= count($daftar) ?> santri.</p>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover mb-0">
      <thead class="table-success">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>L/P</th>
          <th>Tanggal Lahir</th>
          <th>Umur</th>
          <th>Asal Daerah</th>
          <th>Kamar</th>
          <th>No. HP Wali</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($daftar) === 0): ?>
          <tr>
            <td colspan="9" class="text-center text-muted">Belum ada data santri.</td>
          </tr>
        <?php endif; ?>


        <?php foreach ($daftar as $no => $s): ?>
          <?php
          $tanggalLahir = new DateTime($s['tanggal_lahir']);
          $hariIni = new DateTime();
          $umur = $hariIni->diff($tanggalLahir)->y;
          ?>
          <tr>
            <td><?= $offset + $no + 1 ?></td>
            <td><?= e($s['nama']) ?></td>
            <td>
              <?php
              if ($s['jenis_kelamin'] == 'L') {
                echo 'Laki-laki';
              } else {
                echo 'Perempuan';
              }
              ?>
            </td>
            <td><?= e($s['tanggal_lahir']) ?></td>
            <td><?= $umur ?> tahun</td>
            <td><?= e($s['asal_daerah']) ?></td>
            <td><?= e($s['kamar']) ?></td>
            <td><?= e($s['no_hp_wali']) ?></td>

            <td>
              <a href="edit.php?id=<?= $s['id'] ?>"
                class="btn btn-sm btn-warning">Edit</a>

              <button type="button"
                class="btn btn-sm btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#modalHapus"
                data-id="<?= $s['id'] ?>"
                data-nama="<?= e($s['nama']) ?>">
                Hapus
              </button>
            </td>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </div>
</div>

<!-- KODE PAGINATION DITARUH DI SINI -->

<?php
$queryHalaman = [
  'cari' => $cari,
  'kamar' => $kamar,
];

$totalTombol = $totalHalaman;
?>

<?php if ($totalData > 0): ?>
  <nav aria-label="Navigasi halaman">
    <ul class="pagination justify-content-center">
      <li class="page-item <?= $halaman <= 1 ? 'disabled' : '' ?>">
        <a class="page-link"
          href="?<?= e(http_build_query($queryHalaman + ['halaman' => max(1, $halaman - 1)])) ?>">
          Sebelumnya
        </a>
      </li>

      <?php for ($i = 1; $i <= $totalTombol; $i++): ?>
        <li class="page-item <?= $i === $halaman ? 'active' : '' ?>">
          <a class="page-link"
            href="?<?= e(http_build_query($queryHalaman + ['halaman' => $i])) ?>">
            <?= $i ?>
          </a>
        </li>
      <?php endfor; ?>

      <li class="page-item <?= $halaman >= $totalHalaman ? 'disabled' : '' ?>">
        <a class="page-link"
          href="?<?= e(http_build_query($queryHalaman + ['halaman' => min($totalHalaman, $halaman + 1)])) ?>">
          Selanjutnya
        </a>
      </li>
    </ul>
  </nav>
<?php endif; ?>

<div class="modal fade" id="modalHapus" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" action="hapus.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Yakin ingin menghapus data <strong id="namaHapus"></strong>?
        <input type="hidden" name="id" id="idHapus">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById('modalHapus').addEventListener('show.bs.modal', function(event) {
    var tombol = event.relatedTarget;
    document.getElementById('idHapus').value = tombol.getAttribute('data-id');
    document.getElementById('namaHapus').textContent = tombol.getAttribute('data-nama');
  });
</script>

<?php include 'footer.php'; ?>