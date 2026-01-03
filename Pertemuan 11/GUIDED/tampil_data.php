<?php 
// File: tampil_data.php 
// Fungsi: Menampilkan semua data mahasiswa dengan fitur pencarian
include "koneksi.php"; 
include "proses_cari.php"; 
?> 
 
<!DOCTYPE html> 
<html> 
 
<head> 
    <title>Data Mahasiswa</title> 
    <style> 
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
        } 
 
        h2 { 
            color: #333; 
        } 
 
        .search-box { 
            margin: 20px 0; 
            padding: 20px; 
            background-color: #f9f9f9; 
            border-radius: 5px; 
        } 
        .search-box input { 
            padding: 10px; 
            width: 300px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
        } 
 
        .search-box button { 
            padding: 10px 20px; 
            background-color: #2196F3; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-right: 10px; 
        } 
 
        .search-box button:hover { 
            background-color: #0b7dda; 
        } 
 
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        } 
 
        th { 
            background-color: #4CAF50; 
            color: white; 
            padding: 12px; 
            text-align: left; 
        } 
 
        td { 
            padding: 10px; 
            border-bottom: 1px solid #ddd; 
        } 
        tr:hover { 
            background-color: #f5f5f5; 
        } 
 
        .btn { 
            padding: 5px 10px; 
            text-decoration: none; 
            border-radius: 3px; 
            color: white; 
            font-size: 12px; 
        } 
 
        .btn-edit { 
            background-color: #2196F3; 
        } 
 
        .btn-delete { 
            background-color: #f44336; 
        } 
 
        .btn-add { 
            background-color: #4CAF50; 
            padding: 10px 20px; 
            margin-bottom: 20px; 
            display: inline-block; 
        } 
 
        .info-text { 
            margin: 10px 0; 
            font-size: 14px; 
        } 
 
        .keyword-highlight { 
            color: #2196F3; 
            font-weight: bold; 
        } 
    </style> 
    </head> 
 
<body> 
    <h2>Data Mahasiswa</h2> 
 
    <a href="form_tambah.php" class="btn btn-add">Tambah Data Baru</a> 
 
    <div class="search-box"> 
        <form method="GET" action=""> 
            <input type="text" name="keyword" 
                placeholder="Cari berdasarkan NIM, Nama, atau Jurusan..." 
                value="<?php echo isset($_GET['keyword']) ? 
htmlspecialchars($_GET['keyword']) : ''; ?>"> 
            <button class="btn btn-search" type="submit">Cari</button> 
            <?php if (isset($_GET['keyword']) && $_GET['keyword'] != ''): ?> 
                <a href="tampil_data.php"> 
                    <button type="button">Reset / Tampilkan Semua</button> 
                </a> 
            <?php endif; ?> 
        </form> 
    </div> 
 
    <?php 
    // Cek apakah ada keyword pencarian 
    if (isset($_GET['keyword']) && $_GET['keyword'] != '') { 
        $keyword = $_GET['keyword']; 
 
        // Panggil fungsi pencarian dari fungsi_cari.php 
        $result = cariMahasiswa($conn, $keyword); 
        $jumlah = hitungHasilCari($result); 
 
        echo "<p class='info-text'>Ditemukan <strong>$jumlah</strong> data dengan keyword:  
              <span class='keyword-highlight'>'" . htmlspecialchars($keyword) . "'</span></p>"; 
    } else { 
        // Query untuk menampilkan semua data 
        $query = "SELECT * FROM mahasiswa ORDER BY nim ASC"; 
        $result = mysqli_query($conn, $query); 
        $jumlah = mysqli_num_rows($result); 
        echo "<p class='info-text'>Menampilkan <strong>$jumlah</strong> data mahasiswa</p>"; 
    } 
    ?> 
 
    <table> 
        <thead> 
            <tr> 
                <th>No</th> 
                <th>NIM</th> 
                <th>Nama</th> 
                <th>Jurusan</th> 
                <th>Email</th> 
                <th>Tanggal Lahir</th> 
                <th>Aksi</th> 
            </tr> 
        </thead> 
        <tbody> 
            <?php 
            // Cek apakah ada data 
            if (mysqli_num_rows($result) > 0) { 
                $no = 1; 
                // Loop untuk menampilkan setiap baris data 
                while ($row = mysqli_fetch_assoc($result)) { 
                    echo "<tr>"; 
                    echo "<td>" . $no++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['nim']) . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['jurusan']) . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['tanggal_lahir']) . "</td>"; 
                    echo "<td> 
                            <a href='form_edit.php?nim=" . urlencode($row['nim']) . "'  
                               class='btn btn-edit'>Edit</a> 
                            <a href='proses_hapus.php?nim=" . urlencode($row['nim']) . "'  
                               class='btn btn-delete' 
                               onclick=\"return confirm('Yakin ingin menghapus data ini?')\"> 
                               Hapus</a> 
                          </td>"; 
                    echo "</tr>"; 
                } 
            } else { 
                if (isset($_GET['keyword']) && $_GET['keyword'] != '') { 
                    echo "<tr><td colspan='7' style='text-align:center; color: red;'> 
                          Data tidak ditemukan dengan keyword tersebut!</td></tr>"; 
                } else { 
                    echo "<tr><td colspan='7' style='text-align:center'> 
                          Tidak ada data</td></tr>"; 
                } 
            } 
 
            mysqli_close($conn); 
            ?> 
        </tbody> 
    </table> 
</body> 
 
</html> 