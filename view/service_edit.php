<?php
// view/service_edit.php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once(__DIR__ . '/../controller/auth/db_connection.php');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { echo "ID tidak valid"; exit; }

$s = $conn->prepare("SELECT * FROM tbl_service WHERE service_id = ? LIMIT 1");
$s->bind_param("i", $id); $s->execute();
$r = $s->get_result()->fetch_assoc();
if (!$r) { echo "Data tidak ditemukan"; exit; }
?>
<div class="container">
  <h3>Edit Service #<?= $r['service_id'] ?></h3>
  <form method="post" action="/controller/service/pro_service.php?action=edit">
    <input type="hidden" name="service_id" value="<?= $r['service_id'] ?>">
    <div class="mb-2"><label>Pelanggan</label><input class="form-control" name="pelanggan" value="<?= htmlspecialchars($r['pelanggan']) ?>"></div>
    <div class="mb-2"><label>Tanggal Masuk</label><input type="date" class="form-control" name="tgl_masuk" value="<?= htmlspecialchars($r['tgl_masuk']) ?>"></div>
    <div class="mb-2"><label>Tanggal Keluar</label><input type="date" class="form-control" name="tgl_keluar" value="<?= htmlspecialchars($r['tgl_keluar']) ?>"></div>
    <div class="mb-2"><label>Biaya Service</label><input class="form-control" name="biaya_service" value="<?= htmlspecialchars($r['biaya_service']) ?>"></div>
    <div class="mb-2"><label>Biaya Jasa</label><input class="form-control" name="biaya_jasa" value="<?= htmlspecialchars($r['biaya_jasa']) ?>"></div>
    <div class="mb-2"><label>Biaya Bahan</label><input class="form-control" name="biaya_bahan" value="<?= htmlspecialchars($r['biaya_bahan']) ?>"></div>
    <div class="mb-2"><label>Biaya Gaji</label><input class="form-control" name="biaya_gaji" value="<?= htmlspecialchars($r['biaya_gaji']) ?>"></div>
    <div class="mb-2"><label>Status</label>
      <select class="form-select" name="status">
        <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Pending</option>
        <option value="progress" <?= $r['status']=='progress'?'selected':'' ?>>Progress</option>
        <option value="done" <?= $r['status']=='done'?'selected':'' ?>>Done</option>
      </select>
    </div>
    <div class="mb-2"><label>Keterangan</label><textarea class="form-control" name="keterangan"><?= htmlspecialchars($r['keterangan']) ?></textarea></div>
    <button class="btn btn-primary" type="submit">Simpan</button>
    <a class="btn btn-secondary" href="/view/service_list.php">Kembali</a>
  </form>
</div>
