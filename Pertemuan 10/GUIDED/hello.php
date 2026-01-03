<?php
// echo "Hello World";
// echo "Nama Saya Naura";
// echo "Kelas SE0702";

// $nama = "Naura";
// $nim = "2311104078";
// $hobi = "Tidur";

// echo "Nama: " . $nama;
// echo "<br>";
// echo "NIM: " . $nim;
// echo "<br>"
// echo "Hobi: " . $hobi;

// define("Nama" , "Naura");
// define("NIM" , "2311104078");
// define("Asal", "Bekasi");

// echo "Nama: " . Nama . "<br>";
// echo "NIM: " . NIM . "<br>";
// echo "Asal: " . Asal;

// $nilai = 90;

// if ($nilai > 50) {
//     echo "Nilai anda adalah: " . $nilai . "Selamat, Anda lulus";
// } else {
//     echo "Nilai anda adalah: " . $nilai . "Maaf, Anda tidak lulus";
// }

// $nilai = 80; 

// switch ($nilai) {  
//     case ($nilai > 50 && $nilai <= 60): 
//         echo "Nilai Anda adalah $nilai. Indeks nilai anda C"; 
//         break; 
//     case ($nilai > 60 && $nilai <= 70): 
//         echo "Nilai Anda adalah $nilai. Indeks nilai anda BC"; 
//         break; 
//     case ($nilai > 70 && $nilai <= 75): 
//         echo "Nilai Anda adalah $nilai. Indeks nilai anda B"; 
//         break; 
//     case ($nilai > 75 && $nilai <= 80): 
//         echo "Nilai Anda adalah $nilai. Indeks nilai anda AB"; 
//         break; 
//     case ($nilai > 80 && $nilai <= 100): 
//         echo "Nilai Anda adalah $nilai. Indeks nilai anda A"; 
//         break; 
//     default: 
//         echo "Nilai Anda adalah $nilai. Maaf, Anda tidak lulus"; 
//         break; 
// }

// echo "Ini adalah contoh perulangan for"; 
// echo "<br>"; 
// for ($i = 1; $i <= 10; $i++) { 
//     echo $i . " "; 
// }

// echo "<br>"; 
// echo "<br>"; 
// echo "Ini adalah contoh perulangan while"; 
// echo "<br>"; 
// $i = 1; 
// while ($i <= 20) { 
//     echo $i . " "; 
//     $i += 2;  
// } 
 
// echo "<br>"; 
// echo "<br>"; 
// echo "Ini adalah contoh perulangan do-while"; 
// echo "<br>"; 
// $i = 1; 
// do { 
//     echo $i . " "; 
//     $i += 3; 
// } while ($i < 30);

// function cetakGenap() 
// { 
//     for ($i = 1; $i <= 100; $i++) { 
//         if ($i % 2 == 0) { 
//             echo "$i "; 
//         } 
//     } 
// } 
// //pemanggilan fungsi 
// cetakGenap(); 

// function cetakGenap($awal, $akhir) 
// { 
//     for ($i = $awal; $i <= $akhir; $i++) { 
//         if ($i % 2 == 0) { 
//         echo "$i "; 
//         } 
//     } 
// } 
// //pemanggilan fungsi 
// $a = 10; 
// $b = 50; 
// echo "Bilangan ganjil dari $a sampai $b adalah : <br>"; 
// cetakGenap($a, $b); 

// function luasSegitiga($alas, $tinggi) 
// { 
//     return 0.5 * $alas * $tinggi; 
// }  
// //pemanggilan fungsi 
// $a = 10; 
// $t = 50; 
// echo "Luas Segitiga dengan alas $a dan tinggi $t adalah : " . luasSegitiga($a, $t); 

// $arrKendaraan = ["Mobil", "Pesawat", "Kereta Api", "Kapal Laut"]; 
// echo $arrKendaraan[0] . "<br>"; //Mobil 
// echo $arrKendaraan[2] . "<br>"; //Kereta Api 

// $arrKota = [];  
// $arrKota[] = "Jakarta"; 
// $arrKota[] = "Medan"; 
// $arrKota[] = "Bandung"; 
// $arrKota[] = "Malang"; 
// $arrKota[] = "Sulawesi"; 

// echo $arrKota[1] . "<br>"; //Medan 
// echo $arrKota[2] . "<br>"; //Bandung 
// echo $arrKota[4] . "<br>"; //Sulawesi 

$arrAlamat = [ 
"Rona" => "Banjarmasin", 
"Dhiva" => "Bandung", 
"Ilham" => "Medan", 
"Oku" => "Hongkong", 
]; 
 
echo $arrAlamat["Dhiva"] . "<br>"; //Bandung 
echo $arrAlamat['Oku'] . "<br>"; //Hongkong 
 
$arrNim = []; 
$arrNim["Rona"] = "11011112"; 
$arrNim["Dhiva"] = "11011101"; 
$arrNim["Ilham"] = "11011309"; 
$arrNim["Oku"] = "11014765"; 
$arrNim["Fadhlan"] = "11011113"; 
 
echo $arrNim["Ilham"] . "<br>"; //11011309 
echo $arrNim['Fadhlan'] . "<br>"; //11011113 
?>