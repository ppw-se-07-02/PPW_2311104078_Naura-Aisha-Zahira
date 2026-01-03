<!DOCTYPE html> 
<html lang="en"> 
    <head> 
        <title>Data Produk</title> 
    </head> 
    <body> 
        Produk 1 : {{ $prod1; }} 
        <br> 
        Produk 2 : {{ $prod2; }} 
        <br> 
        Produk 2 : {{ $prod3; }} 
    </body> 
</html> 


<!-- @extends('master') 
@section('title','Data Produk') 
 
@section('content') 
    Produk 1 : {{ $prod1; }} 
    <br> 
    Produk 2 : {{ $prod2; }} 
    <br> 
    Produk 2 : {{ $prod3; }} 
@endsection  -->


<!-- @switch($nilai) 
@case(0) 
    Sangat Buruk 
@break 
@case(75) 
    Baik 
@break 
@case(100) 
    Sempurna 
@break 
@default 
    Nilai tidak valid 
@endswitch  -->
