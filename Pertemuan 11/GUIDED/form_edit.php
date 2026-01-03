<?php 
// File: form_edit.php 
// Fungsi: Menampilkan form edit dengan data yang sudah ada 
 
include "koneksi.php"; 
 
// Ambil NIM dari URL 
$nim = $_GET['nim']; 

// Query untuk mengambil data mahasiswa berdasarkan NIM 
$query = "SELECT * FROM mahasiswa WHERE nim='$nim'"; 
$result = mysqli_query($conn, $query); 
$data = mysqli_fetch_assoc($result); 
 
// Jika data tidak ditemukan 
if (!$data) { 
    echo "<script> 
            alert('Data tidak ditemukan!'); 
            window.location.href='tampil_data.php'; 
          </script>"; 
    exit; 
} 
?> 
 
<!DOCTYPE html> 
<html> 
<head> 
    <title>Edit Data Mahasiswa</title> 
    <style> 
        body { 
            font-family: Arial, sans-serif; 
            max-width: 500px; 
            margin: 50px auto; 
            padding: 20px; 
        } 
        .form-group { 
            margin-bottom: 15px; 
        } 
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
        } 
        input, select { 
            width: 100%; 
            padding: 8px; 
             border: 1px solid #ddd; 
            border-radius: 4px; 
            box-sizing: border-box; 
        } 
        button { 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            margin-right: 10px; 
        } 
        .btn-update { 
            background-color: #2196F3; 
            color: white; 
        } 
        .btn-cancel { 
            background-color: #999; 
            color: white; 
        } 
    </style> 
</head> 
<body> 
    <h2>Edit Data Mahasiswa</h2> 
     
    <form method="POST" action="proses_edit.php"> 
        <!-- NIM tidak bisa diubah, jadi dibuat readonly --> 
        <div class="form-group"> 
            <label>NIM:</label> 
            <input type="text" name="nim" value="<?php echo $data['nim']; ?>"  
                   readonly style="background-color: #f0f0f0;"> 
        </div> 
         
        <div class="form-group"> 
            <label>Nama:</label> 
            <input type="text" name="nama" value="<?php echo $data['nama']; ?>"  
                   required maxlength="50"> 
        </div>
         <div class="form-group"> 
            <label>Jurusan:</label> 
            <select name="jurusan" required> 
                <option value="Teknik Informatika"  
                    <?php if($data['jurusan']=='Teknik Informatika') echo 'selected'; ?>> 
                    Teknik Informatika</option> 
                <option value="Sistem Informasi" 
                    <?php if($data['jurusan']=='Sistem Informasi') echo 'selected'; ?>> 
                    Sistem Informasi</option> 
                <option value="Teknologi Informasi" 
                    <?php if($data['jurusan']=='Teknologi Informasi') echo 'selected'; ?>> 
                    Teknologi Informasi</option> 
                <option value="Ilmu Komputer" 
                    <?php if($data['jurusan']=='Ilmu Komputer') echo 'selected'; ?>> 
                    Ilmu Komputer</option> 
            </select> 
        </div> 
         
        <div class="form-group"> 
            <label>Email:</label> 
            <input type="email" name="email" value="<?php echo $data['email']; ?>"  
                   maxlength="50"> 
        </div> 
         
        <div class="form-group"> 
            <label>Tanggal Lahir:</label> 
            <input type="date" name="tanggal_lahir"  
                   value="<?php echo $data['tanggal_lahir']; ?>"> 
        </div> 
         
        <button type="submit" class="btn-update">Update Data</button> 
        <a href="tampil_data.php"> 
            <button type="button" class="btn-cancel">Batal</button> 
        </a> 
    </form> 
</body> 
</html> 

<?php 
mysqli_close($conn); 
?> 