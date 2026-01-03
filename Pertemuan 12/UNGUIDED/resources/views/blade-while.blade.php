<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blade While Loop</title>
</head>
<body>
    <div class="container">
        <header>
            <h1>Latihan Blade: Perulangan WHILE</h1>
            <p>Menampilkan bilangan 1 sampai 10</p>
        </header>

        <div style="background: #f5f5f5; padding: 20px; border-radius: 10px;">
            <h3>Hasil Perulangan WHILE:</h3>
            <ul style="font-size: 18px; line-height: 2;">
                @php
                    $angka = 1;
                @endphp
                
                @while ($angka <= 10)
                    <li>Bilangan ke-{{ $angka }}</li>
                    @php
                        $angka++;
                    @endphp
                @endwhile
            </ul>
        </div>
    </div>
</body>
</html>