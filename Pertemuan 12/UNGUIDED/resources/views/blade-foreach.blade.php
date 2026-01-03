<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blade Foreach Loop</title>
</head>
<body>
    <div class="container">
        <header>
            <h1>Latihan Blade: Perulangan FOREACH</h1>
            <p>Menampilkan nilai dari array</p>
        </header>

        <div style="background: #f5f5f5; padding: 20px; border-radius: 10px;">
            <h3>Data Nilai Mahasiswa:</h3>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background: #4CAF50; color: white;">
                        <th style="padding: 12px; border: 1px solid #ddd;">No</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Nilai</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nilai as $index => $n)
                        <tr style="background: {{ $n >= 60 ? '#c8e6c9' : '#ffcdd2' }};">
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">{{ $index + 1 }}</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center; font-weight: bold;">{{ $n }}</td>
                            <td style="padding: 12px; border: 1px solid #ddd; text-align: center;">
                                @if ($n >= 60)
                                    <span style="color: green; font-weight: bold;">✓ Lulus</span>
                                @else
                                    <span style="color: red; font-weight: bold;">✗ Tidak Lulus</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 30px; padding: 15px; background: white; border-radius: 5px;">
                <h4>Statistik:</h4>
                <p><strong>Jumlah Mahasiswa:</strong> {{ count($nilai) }}</p>
                <p><strong>Nilai Tertinggi:</strong> {{ max($nilai) }}</p>
                <p><strong>Nilai Terendah:</strong> {{ min($nilai) }}</p>
                <p><strong>Rata-rata:</strong> {{ number_format(array_sum($nilai) / count($nilai), 2) }}</p>
            </div>
        </div>
    </div>
</body>
</html>