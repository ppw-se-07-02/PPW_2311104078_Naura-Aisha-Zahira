<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presensi Siswa - TPQSmart</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .table-container {
            margin-top: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background-color: #83D1A7;
            color: white;
        }
        
        table tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn-simpan {
            background: #83D1A7;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-simpan:hover {
            background: #83D1A7;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="{{ asset('assets/img/TPQSmart Logo.png') }}" alt="Logo TPQ">
            <h1>Form Presensi Siswa</h1>
            <p>Kelas A - Tanggal: <span id="tanggal-hari-ini"></span></p>
        </header>

        <div class="table-container">
            <table id="tabel-presensi">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ahmad Fauzi</td>
                        <td>
                            <select>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Siti Aminah</td>
                        <td>
                            <select>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Muhammad Rizki</td>
                        <td>
                            <select>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn-simpan" onclick="simpanPresensi()">Simpan Presensi</button>
        </div>
    </div>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script>
        // Tampilkan tanggal hari ini
        document.getElementById('tanggal-hari-ini').textContent = new Date().toLocaleDateString('id-ID');
        
        // Fungsi simpan presensi
        function simpanPresensi() {
            alert('Data presensi berhasil disimpan!\n\nNotifikasi WhatsApp akan dikirim ke orang tua/wali.');
            console.log('Presensi disimpan pada:', new Date());
        }
    </script>
</body>
</html>