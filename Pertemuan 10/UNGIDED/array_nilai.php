<?php
// Masukkan data array nilai mahasiswa 
$nilai = [75, 89, 65, 90, 85, 70, 98, 65, 69, 70, 12]; 

// Mencari nilai tertinggi menggunakan fungsi max() 
$tertinggi = max($nilai); 

// Mencari nilai terendah menggunakan fungsi min() 
$terendah = min($nilai); 

// Menghitung rata-rata nilai [cite: 475]
$rata_rata = array_sum($nilai) / count($nilai); 

// Mengurutkan nilai dari tertinggi ke terendah 
rsort($nilai); 

echo "<h2>Manipulasi Array</h2>";
echo "Nilai Tertinggi: $tertinggi <br>";
echo "Nilai Terendah: $terendah <br>";
echo "Rata-rata: " . number_format($rata_rata, 2) . "<br>";
echo "Urutan Nilai: " . implode(", ", $nilai);
?>