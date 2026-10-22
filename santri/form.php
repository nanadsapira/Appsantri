<form method="post" class="card card-body shadow-sm">
    <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" value="<?= e($dataForm['nama'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="L" <?= ($dataForm['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
            <option value="P" <?= ($dataForm['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="form-control"
            value="<?= e($dataForm['tanggal_lahir'] ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Asal Daerah</label>
        <input type="text" name="asal_daerah" class="form-control"
            value="<?= e($dataForm['asal_daerah'] ?? '') ?>">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Kamar</label>
            <input type="text" name="kamar" class="form-control"
                value="<?= e($dataForm['kamar'] ?? '') ?>">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">No. HP Wali</label>
            <input type="text" name="no_hp_wali" class="form-control"
                value="<?= e($dataForm['no_hp_wali'] ?? '') ?>">
        </div>
    </div>

    <div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </div>
</form>