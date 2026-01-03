<?php
$c = 30; // Input suhu Celcius
$f = ($c * 9/5) + 32;
$k = $c + 273.15;

echo "<h2>Konversi Suhu</h2>";
echo "Celcius: $c °C <br>";
echo "Fahrenheit: " . number_format($f, 2) . " °F <br>";
echo "Kelvin: " . number_format($k, 2) . " K";
?>