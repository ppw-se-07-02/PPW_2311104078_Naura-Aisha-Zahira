<!DOCTYPE html> 
<html> 
<head> 
    <title>Tambah Data Mahasiswa</title> 
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
            background-color: #4CAF50; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
        } 
        button:hover { 
            background-color: #45a049; 
        } 
    </style> 
</head> 
<body> 
    <h2>          Tambah Data Mahasiswa</h2> 
     
    <form method="POST" action="proses_tambah.php"> 
        <div class="form-group"> 
            <label>NIM:</label> 
            <input type="text" name="nim" required maxlength="10"  
                   placeholder="Contoh: 2024001"> 
        </div> 
         
        <div class="form-group"> 
            <label>Nama:</label> 
            <input type="text" name="nama" required maxlength="50" 
                   placeholder="Masukkan nama lengkap"> 
        </div> 
         
        <div class="form-group"> 
            <label>Jurusan:</label> 
            <select name="jurusan" required> 
                <option value="">-- Pilih Jurusan --</option> 
                <option value="Teknik Informatika">Teknik Informatika</option> 
                <option value="Sistem Informasi">Sistem Informasi</option> 
                <option value="Teknologi Informasi">Sains data</option> 
                <option value="Ilmu Komputer">Rekayasa Perangkat Lunak</option> 
            </select> 
        </div> 
         
        <div class="form-group"> 
            <label>Email:</label> 
            <input type="email" name="email" maxlength="50" 
                   placeholder="contoh@email.com"> 
        </div> 
         
        <div class="form-group"> 
            <label>Tanggal Lahir:</label> 
            <input type="date" name="tanggal_lahir"> 
        </div> 
         
        <button type="submit">Simpan Data</button> 
        <a href="tampil_data.php"> 
            <button type="button">Lihat Data</button> 
        </a> 
    </form> 
</body> 
</html> 