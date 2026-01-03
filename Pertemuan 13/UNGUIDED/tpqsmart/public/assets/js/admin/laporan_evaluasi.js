// ========== LAPORAN EVALUASI SCRIPT - FIXED VERSION ==========

let currentData = [];
let filteredData = [];
let currentFilter = "all";

$(document).ready(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Load data dari window
    if (window.evaluasiData && window.evaluasiData.evaluasiList) {
        currentData = window.evaluasiData.evaluasiList;
        filteredData = [...currentData];

        console.log("✅ Data berhasil dimuat:", currentData);

        // Update statistics
        updateStatistics();
    }

    // Setup event listeners
    setupEventListeners();

    console.log("✅ Laporan Evaluasi loaded successfully");
});

// ========== UPDATE STATISTICS ==========
function updateStatistics() {
    const total = currentData.length;
    const melanjutkan = currentData.filter(
        (d) => d.kemampuan === "melanjutkan"
    ).length;
    const mengulangi = currentData.filter(
        (d) => d.kemampuan === "mengulangi"
    ).length;

    const progressRate =
        total > 0 ? Math.round((melanjutkan / total) * 100) : 0;
    const persenMelanjutkan =
        total > 0 ? Math.round((melanjutkan / total) * 100) : 0;
    const persenMengulangi =
        total > 0 ? Math.round((mengulangi / total) * 100) : 0;

    // Update stat cards
    $("#totalSiswa").text(total);
    $("#totalMelanjutkan").text(melanjutkan);
    $("#totalMengulangi").text(mengulangi);
    $("#progressRate").text(progressRate + "%");
    $("#persenMelanjutkan").text(persenMelanjutkan + "% dari total");
    $("#persenMengulangi").text(persenMengulangi + "% dari total");

    // Update filter counts
    $("#countAll").text(total);
    $("#countMelanjutkan").text(melanjutkan);
    $("#countMengulangi").text(mengulangi);

    // Update summary
    $("#displayCount").text(filteredData.length);
    $("#totalCount").text(total);
    $("#summaryMelanjutkan").text(melanjutkan);
    $("#summaryMengulangi").text(mengulangi);
}

// ========== SETUP EVENT LISTENERS ==========
function setupEventListeners() {
    // Search functionality
    $("#searchSiswa").on("input", function () {
        const searchTerm = $(this).val().toLowerCase().trim();
        filterAndDisplay();
    });

    // Filter tabs
    $(".filter-btn").on("click", function () {
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");

        currentFilter = $(this).data("status");
        filterAndDisplay();
    });

    // Class selection
    $("#classDropdown")
        .next(".dropdown-menu")
        .on("click", ".dropdown-item", function (e) {
            e.preventDefault();
            const kelas = $(this).find("strong").text();
            selectClass(kelas);
        });

    // Date picker
    $("#evaluasiDate").on("change", function () {
        const newDate = $(this).val();
        const selectedClass = window.evaluasiData.selectedClass;

        if (selectedClass) {
            loadDataByClassAndDate(selectedClass, newDate);
        } else {
            window.location.href =
                window.location.pathname + "?tanggal=" + newDate;
        }
    });

    // Export button
    $("#btnExport").on("click", exportToExcel);

    // Logout button
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").addClass("show");
    });

    $("#cancelLogout").on("click", function () {
        $("#logoutOverlay").removeClass("show");
    });

    $("#logoutOverlay").on("click", function (e) {
        if ($(e.target).hasClass("logout-overlay")) {
            $(this).removeClass("show");
        }
    });
}

// ========== FILTER AND DISPLAY ==========
function filterAndDisplay() {
    const searchTerm = $("#searchSiswa").val().toLowerCase().trim();

    // Apply status filter
    let filtered = currentData;
    if (currentFilter !== "all") {
        filtered = currentData.filter(
            (item) => item.kemampuan === currentFilter
        );
    }

    // Apply search filter
    if (searchTerm) {
        filtered = filtered.filter(
            (item) =>
                item.student_name.toLowerCase().includes(searchTerm) ||
                item.student_id.toLowerCase().includes(searchTerm)
        );
    }

    filteredData = filtered;

    // Update display
    renderTable();
    updateSummaryCount();
}

// ========== RENDER TABLE ==========
function renderTable() {
    const tbody = $("#evaluasiTableBody");
    tbody.empty();

    if (filteredData.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="empty-table">
                        <i class="bi bi-clipboard-data"></i>
                        <h5>Tidak Ada Data</h5>
                        <p>Tidak ditemukan data evaluasi yang sesuai</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    filteredData.forEach((item) => {
        const statusClass =
            item.kemampuan === "melanjutkan" ? "melanjutkan" : "mengulangi";
        const statusText =
            item.kemampuan.charAt(0).toUpperCase() + item.kemampuan.slice(1);

        const row = `
            <tr>
                <td>${item.student_id}</td>
                <td><strong>${item.student_name}</strong></td>
                <td><span class="tilawati-text">${
                    item.tilawati || "-"
                }</span></td>
                <td>
                    <span class="status-badge ${statusClass}">
                        ${statusText}
                    </span>
                </td>
                <td>
                    <button class="btn-edit-presensi" onclick="viewDetail(${
                        item.id
                    }, '${item.student_name}')">
                        <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// ========== UPDATE SUMMARY COUNT ==========
function updateSummaryCount() {
    $("#displayCount").text(filteredData.length);
    $("#totalCount").text(currentData.length);
}

// ========== SELECT CLASS ==========
function selectClass(kelas) {
    const date = $("#evaluasiDate").val() || window.evaluasiData.selectedDate;

    // Show loading
    showLoading();

    // Redirect dengan parameter
    window.location.href =
        window.location.pathname +
        "?kelas=" +
        encodeURIComponent(kelas) +
        "&tanggal=" +
        date;
}

// ========== LOAD DATA BY CLASS AND DATE ==========
function loadDataByClassAndDate(kelas, tanggal) {
    showLoading();

    $.ajax({
        url: "/admin/laporan-evaluasi/by-class",
        type: "GET",
        data: {
            kelas: kelas,
            tanggal: tanggal,
        },
        success: function (response) {
            if (response.success) {
                currentData = response.data;
                filteredData = [...currentData];

                // Update UI
                updateStatistics();
                renderTable();

                showNotification("Data berhasil dimuat!", "success");
            } else {
                showNotification("Gagal memuat data!", "error");
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            showNotification("Terjadi kesalahan saat memuat data!", "error");
        },
        complete: function () {
            hideLoading();
        },
    });
}

// ========== VIEW DETAIL ==========
window.viewDetail = function (siswaId, siswaNama) {
    // Show loading di button
    const button = event.target.closest("button");
    const originalHtml = $(button).html();
    $(button)
        .prop("disabled", true)
        .html('<i class="bi bi-hourglass-split"></i>');

    const tanggal =
        $("#evaluasiDate").val() || window.evaluasiData.selectedDate;

    $.ajax({
        url: "/admin/laporan-evaluasi/detail",
        type: "GET",
        data: {
            siswa_id: siswaId,
            tanggal: tanggal,
        },
        success: function (response) {
            if (response.success && response.data) {
                const siswa = response.data.siswa;
                const perkembangan = response.data.perkembangan;

                // Set modal data
                $("#modalStudentName").text(`${siswa.nama} - ${siswa.nis}`);

                if (siswa.foto) {
                    $("#modalPhoto").attr("src", siswa.foto);
                } else {
                    $("#modalPhoto").attr(
                        "src",
                        "/assets/img/default-avatar.png"
                    );
                }

                if (perkembangan) {
                    $("#modalTilawati").text(perkembangan.tilawati || "-");
                    $("#modalKemampuan").text(perkembangan.kemampuan || "-");
                    $("#modalHafalan").text(perkembangan.hafalan || "-");
                    $("#modalTataKrama").text(perkembangan.tata_krama || "-");
                    $("#modalCatatan").text(perkembangan.catatan || "-");
                } else {
                    $("#modalTilawati").text("Belum ada data");
                    $("#modalKemampuan").text("Belum ada data");
                    $("#modalHafalan").text("Belum ada data");
                    $("#modalTataKrama").text("Belum ada data");
                    $("#modalCatatan").text("Belum ada data");
                }

                // Show modal
                const modal = new bootstrap.Modal(
                    document.getElementById("detailModal")
                );
                modal.show();
            } else {
                showNotification("Gagal mengambil detail!", "error");
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            showNotification("Terjadi kesalahan!", "error");
        },
        complete: function () {
            $(button).prop("disabled", false).html(originalHtml);
        },
    });
};

// ========== EXPORT TO EXCEL ==========
function exportToExcel() {
    if (filteredData.length === 0) {
        showNotification("Tidak ada data untuk di-export!", "warning");
        return;
    }

    const exportData = filteredData.map((item, index) => ({
        No: index + 1,
        NIS: item.student_id,
        "Nama Siswa": item.student_name,
        Tilawati: item.tilawati,
        Kemampuan:
            item.kemampuan.charAt(0).toUpperCase() + item.kemampuan.slice(1),
    }));

    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Evaluasi");

    const date = $("#evaluasiDate").val() || window.evaluasiData.selectedDate;
    const kelas = window.evaluasiData.selectedClass || "All";
    const filename = `Laporan_Evaluasi_${kelas}_${date}.xlsx`;

    XLSX.writeFile(wb, filename);

    showNotification("Data berhasil di-export!", "success");
}

// ========== LOADING FUNCTIONS ==========
function showLoading() {
    const loadingHtml = `
        <div class="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div class="text-center">
                <i class="bi bi-hourglass-split" style="font-size: 3rem; color: #2eaf7d; animation: spin 1s linear infinite;"></i>
                <p class="mt-3 fw-bold">Memuat data...</p>
            </div>
        </div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `;
    $("body").append(loadingHtml);
}

function hideLoading() {
    $(".loading-overlay").remove();
}

// ========== SHOW NOTIFICATION ==========
function showNotification(message, type = "success") {
    $(".notification-toast").remove();

    const icons = {
        success: "check-circle-fill",
        error: "x-circle-fill",
        warning: "exclamation-circle-fill",
        info: "info-circle-fill",
    };

    const colors = {
        success: "alert-success",
        error: "alert-danger",
        warning: "alert-warning",
        info: "alert-info",
    };

    const notification = `
        <div class="alert ${colors[type]} alert-dismissible fade show position-fixed notification-toast" 
             style="top: 90px; right: 20px; z-index: 9999; min-width: 320px; max-width: 400px;" 
             role="alert">
            <i class="bi bi-${icons[type]} me-2"></i>
            <strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    $("body").append(notification);

    setTimeout(function () {
        $(".notification-toast").fadeOut(300, function () {
            $(this).remove();
        });
    }, 3000);
}

// ========== KEYBOARD SHORTCUTS ==========
$(document).on("keydown", function (e) {
    if (e.ctrlKey && e.key === "f") {
        e.preventDefault();
        $("#searchSiswa").focus();
    }

    if (e.key === "Escape") {
        if (!$(".modal").hasClass("show")) {
            $("#searchSiswa").val("").trigger("input");
            $("#searchSiswa").blur();
        }
    }
});
