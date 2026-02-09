<?php
include_once(__DIR__ . '/../auth/db_connection.php');
$from = $_GET['from'] ?? date('Y-m-01'); // default bulan ini
$to = $_GET['to'] ?? date('Y-m-t');

$sql = "SELECT 
  SUM(IFNULL(biaya_service,0)) AS total_biaya_service,
  SUM(IFNULL(biaya_jasa,0)) AS total_biaya_jasa,
  SUM(IFNULL(biaya_bahan,0)) AS total_biaya_bahan,
  SUM(IFNULL(biaya_gaji,0)) AS total_biaya_gaji,
  SUM((IFNULL(biaya_service,0)+IFNULL(biaya_jasa,0))-(IFNULL(biaya_bahan,0)+IFNULL(biaya_gaji,0))) AS pendapatan
FROM tbl_service
WHERE tgl_masuk BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
echo json_encode($res);
