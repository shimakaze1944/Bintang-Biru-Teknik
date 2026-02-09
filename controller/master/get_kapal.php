<?php
include_once(__DIR__ . '/../auth/db_connection.php');

$vendor_id = intval($_GET['vendor_id'] ?? 0);
if ($vendor_id <= 0) exit;

$q = $conn->prepare("SELECT kapal_id, nama_kapal FROM tbl_kapal WHERE vendor_id = ? ORDER BY nama_kapal ASC");
$q->bind_param("i", $vendor_id);
$q->execute();
$res = $q->get_result();

while ($r = $res->fetch_assoc()) {
  echo "<option value='{$r['nama_kapal']}'>{$r['nama_kapal']}</option>";
}
