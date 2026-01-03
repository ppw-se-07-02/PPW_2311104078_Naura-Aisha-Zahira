// JS TPQSMART


console.log('TPQSmart JavaScript Loaded Successfully!');

// Fungsi untuk menampilkan pesan selamat datang
function tampilkanPesan() {
    const waktu = new Date().getHours();
    let salam;
    
    if (waktu < 11) {
        salam = "Selamat Pagi";
    } else if (waktu < 15) {
        salam = "Selamat Siang";
    } else if (waktu < 19) {
        salam = "Selamat Sore";
    } else {
        salam = "Selamat Malam";
    }
    
    return salam;
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('Halaman TPQSmart siap digunakan!');
    console.log(tampilkanPesan());
    
    // Tambahkan interaksi pada menu
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(function(item) {
        item.addEventListener('click', function() {
            const menuName = this.querySelector('h3').textContent;
            alert('Anda mengklik menu: ' + menuName);
        });
    });
});

// Fungsi untuk logout
function handleLogout() {
    if (confirm('Apakah Anda yakin ingin keluar?')) {
        alert('Anda telah logout dari sistem TPQSmart');
        window.location.href = '/login';
    }
}