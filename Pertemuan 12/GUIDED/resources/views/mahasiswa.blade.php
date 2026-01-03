<!-- <!DOCTYPE html> 
<html> 
    <head> 
        <title>Belajar Laravel</title> 
    </head> 
    <body> 
        <h2> Data Mahasiswa </h2> 
        <ol> 
            <li>Mahasiswa 1</li> 
            <li>Mahasiswa 2</li> 
            <li>Mahasiswa 3</li> 
            <li>Mahasiswa 4</li> 
        </ol> 
    </body> 
</html>  -->

<!-- <!DOCTYPE html> 
<html> 
    <head> 
        <title>Belajar Laravel</title> 
    </head> 
    <body> 
        <h2> Data Mahasiswa </h2> 
        <ol>
            <li><?php echo $mahasiswa[0]; ?></li> 
            <li><?php echo $mahasiswa[1]; ?></li> 
            <li><?php echo $mahasiswa[2]; ?></li> 
            <li><?php echo $mahasiswa[3]; ?></li> 
        </ol>  
    </body> 
</html>  -->


<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <meta charset="UTF-8"> 
        <title>Data Mahasiswa</title> 
    </head> 
    <body> 
        <div> 
            <h1>Data Mahasiswa</h1> 
            <div> 
                <div> 
                    <ol> 
                        @forelse ($mahasiswa as $val) 
                        <li>{{$val}}</li> 
                        @empty 
                        <div>Tidak ada data...</div> 
                        @endforelse 
                    </ol> 
                </div> 
            </div> 
        </div> 
    </body> 
</html> 
