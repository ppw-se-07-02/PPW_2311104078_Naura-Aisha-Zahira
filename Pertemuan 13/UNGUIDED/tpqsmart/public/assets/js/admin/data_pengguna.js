// ========== DATA PENGGUNA SCRIPT - FIXED VERSION (NO DUPLICATION) ==========

$(document).ready(function () {
    console.log("Data Pengguna JS Loaded");

    // 1. Search Functionality for Guru
    $("#searchInput").on("input", function () {
        const searchTerm = $(this).val().toLowerCase();

        // ✅ Gunakan class yang SPESIFIK untuk menghindari konflik
        $("#guruContainer .col-md-6").each(function () {
            const userName = $(this).find(".user-name").text().toLowerCase();

            if (userName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Show "No results" message if no cards are visible
        const visibleCards = $("#guruContainer .col-md-6:visible").length;
        const emptyState = $("#guruContainer .empty-data-card").length;

        if (visibleCards === 0 && searchTerm !== "" && emptyState === 0) {
            if ($("#noResultsGuru").length === 0) {
                $("#guruContainer").append(`
                    <div id="noResultsGuru" class="col-12 text-center py-5">
                        <i class="bi bi-search" style="font-size: 48px; color: #ddd;"></i>
                        <p class="text-muted mt-3">Tidak ada hasil untuk "${searchTerm}"</p>
                    </div>
                `);
            }
        } else {
            $("#noResultsGuru").remove();
        }
    });

    // 2. Search Functionality for Siswa
    $("#searchInputSiswa").on("input", function () {
        const searchTerm = $(this).val().toLowerCase();

        // ✅ Gunakan class spesifik untuk siswa
        $("#siswaContainer .siswa-card-item").each(function () {
            const userName = $(this).find(".user-name").text().toLowerCase();

            if (userName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Show "No results" message if no cards are visible
        const visibleCards = $(
            "#siswaContainer .siswa-card-item:visible"
        ).length;
        const emptyState = $("#siswaContainer .empty-data-card").length;

        if (visibleCards === 0 && searchTerm !== "" && emptyState === 0) {
            if ($("#noResultsSiswa").length === 0) {
                $("#siswaContainer").append(`
                    <div id="noResultsSiswa" class="col-12 text-center py-5">
                        <i class="bi bi-search" style="font-size: 48px; color: #ddd;"></i>
                        <p class="text-muted mt-3">Tidak ada hasil untuk "${searchTerm}"</p>
                    </div>
                `);
            }
        } else {
            $("#noResultsSiswa").remove();
        }
    });

    // 3. Card Hover Animation
    $(".user-card").hover(
        function () {
            $(this).find(".user-avatar").css("transform", "scale(1.05)");
        },
        function () {
            $(this).find(".user-avatar").css("transform", "scale(1)");
        }
    );

    // 4. Add smooth transition to avatar
    $(".user-card .user-avatar").css("transition", "transform 0.3s ease");

    // 5. Tab switch handler
    $('#userTabs button[data-bs-toggle="tab"]').on(
        "shown.bs.tab",
        function (e) {
            const target = $(e.target).attr("data-bs-target");
            console.log("Switched to tab:", target);

            // Clear search when switching tabs
            $("#searchInput").val("");
            $("#searchInputSiswa").val("");
            $("#noResultsGuru").remove();
            $("#noResultsSiswa").remove();

            // ✅ Show all cards again
            $(
                "#guruContainer .col-md-6, #siswaContainer .siswa-card-item"
            ).show();
        }
    );

    // ✅ 6. Prevent multiple initialization
    if (window.dataPenggunaInitialized) {
        console.warn("Data Pengguna already initialized, skipping...");
        return;
    }
    window.dataPenggunaInitialized = true;

    console.log("Data Pengguna initialized successfully");
});
