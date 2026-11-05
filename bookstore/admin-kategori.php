<?php
// Halaman ini dulunya salinan dashboard. Sekarang diarahkan ke dashboard admin.
$qs = $_SERVER['QUERY_STRING'] ?? '';
header("Location: admin-dashboard.php" . ($qs !== '' ? '?' . $qs : ''));
exit;
