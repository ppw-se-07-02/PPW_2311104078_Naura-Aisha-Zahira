$(document).ready(function() {

    // === DATABASE PRODUK ===
    const products = {
        '1': {
            name: "Velvet Lipstick",
            price: "Rp 150.000",
            description: "Formula velvet-matte yang revolusioner, ringan di bibir namun memberikan warna intens yang tahan lama. Diperkaya dengan Vitamin E dan Jojoba Oil.",
            sku: "GL-VL-001",
            images: [
                "assets/img/detail-lipstick-1.webp",
                "assets/img/detail-lipstick-2.webp",
                "assets/img/detail-lipstick-3.webp"
            ]
        },
        '2': {
            name: "Glowing Foundation",
            price: "Rp 250.000",
            description: "Foundation cair dengan hasil akhir glowing natural. Medium coverage yang dapat di-build up. Tahan lama hingga 12 jam.",
            sku: "GL-FN-002",
            images: [
                "assets/img/detail-foundation-1.jpg",
                "assets/img/detail-foundation-2.webp",
                "assets/img/detail-foundation-3.webp"
            ]
        },
        '3': {
            name: "Eyeshadow Palette 'Sunrise'",
            price: "Rp 320.000",
            description: "12 warna matte dan shimmer dalam nuansa hangat 'sunrise'. Pigmentasi tinggi dan mudah di-blend untuk tampilan siang dan malam.",
            sku: "GL-ES-003",
            images: [
                "assets/img/detail-eyeshadow-1.jpg",
                "assets/img/detail-eyeshadow-2.jpg",
                "assets/img/detail-eyeshadow-3.jpeg"
            ]
        },
        '4': { 
            name: "Matte Blush On",
            price: "Rp 120.000",
            description: "Perona pipi matte dengan tekstur selembut sutra. Memberikan rona pipi alami yang segar dan tahan lama sepanjang hari.",
            sku: "GL-BL-004",
            images: [
                "assets/img/detail-blushon-1.jpg",
                "assets/img/detail-blushon-2.webp",
                "assets/img/detail-blushon-3.jpg"
            ]
        },
        '5': {
            name: "Waterproof Mascara",
            price: "Rp 180.000",
            description: "Maskara waterproof yang memberikan efek memanjangkan dan menebalkan bulu mata secara dramatis. Tidak luntur dan smudge-proof.",
            sku: "GL-MS-005",
            images: [
                "assets/img/detail-mascara-1.jpg",
                "assets/img/detail-mascara-2.avif",
                "assets/img/detail-mascara-3.avif"
            ]
        },
        '6': {
            name: "Hydrating Serum",
            price: "Rp 275.000",
            description: "Serum wajah dengan kandungan Hyaluronic Acid dan Centella Asiatica untuk melembabkan, menenangkan, dan memperkuat skin barrier.",
            sku: "GL-SK-006",
            images: [
                "assets/img/detail-serum-1.avif",
                "assets/img/detail-serum-2.avif",
                "assets/img/detail-serum-3.avif"
            ]
        }
    };

    // === FUNGSI: Mengambil Parameter URL ===
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // === LOGIKA UNTUK HALAMAN DETAIL ===
    if ($('#productTitle').length > 0) {
        const productId = getQueryParam('product');
        const product = products[productId];

        if (product) {
            // 1. Mengisi Info Produk
            $('#productTitle').text(product.name);
            $('#productPrice').text(product.price);
            $('#productDescription').text(product.description);
            $('#productMeta').html(`<li><i class="bi bi-check-lg"></i> <strong>SKU:</strong> ${product.sku}</li>`);

            // 2. Mengisi Image Slider
            let indicatorsHtml = '';
            let innerHtml = '';
            
            product.images.forEach((imgSrc, index) => {
                const activeClass = (index === 0) ? 'active' : '';
                indicatorsHtml += `<button type="button" data-bs-target="#productSlider" data-bs-slide-to="${index}" class="${activeClass}"></button>`;
                innerHtml += `
                    <div class="carousel-item ${activeClass}">
                        <img src="${imgSrc}" class="d-block w-100" alt="${product.name} ${index + 1}">
                    </div>`;
            });

            $('#productSliderIndicators').html(indicatorsHtml);
            $('#productSliderInner').html(innerHtml);
        } else {
            $('#productTitle').text('Produk Tidak Ditemukan');
            $('#productDescription').text('Maaf, produk yang Anda cari tidak ada.');
        }
    }


    // === FUNGSI 1: Notifikasi Add to Cart (MENGGUNAKAN ALERT) ===
    $('#addToCartBtn').on('click', function() {
        // Menggunakan pop-up alert() standar yang pasti muncul
        alert("Produk Ditambahkan ke Keranjang");
    });

    // === FUNGSI 2: Filter Pencarian Produk (index.html) ===
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#productGrid .product-card-wrapper').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // === FUNGSI 3: Inisialisasi Carousel ===
    $('.carousel').carousel();

});