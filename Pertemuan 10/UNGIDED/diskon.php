<?php
$total_belanja = 1200000; // Input total belanja
$diskon = 0;

if ($total_belanja >= 1000000) { $diskon = 0.3; }
elseif ($total_belanja >= 500000) { $diskon = 0.2; }
elseif ($total_belanja >= 100000) { $diskon = 0.1; }

$potongan = $total_belanja * $diskon;
$total_bayar = $total_belanja - $potongan;

echo "<h2>Kalkulator Diskon</h2>";
echo "Total Belanja: Rp " . number_format($total_belanja) . "<br>";
echo "Diskon: Rp " . number_format($potongan) . "<br>";
echo "Total Bayar: Rp " . number_format($total_bayar);
?>