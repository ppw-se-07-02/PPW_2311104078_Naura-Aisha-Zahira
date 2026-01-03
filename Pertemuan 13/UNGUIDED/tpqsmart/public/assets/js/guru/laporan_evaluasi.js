// ========== LAPORAN EVALUASI SCRIPT - CONNECTED VERSION ==========

let currentData = [];
let currentCalendarDate = new Date(); // Variable global untuk kalender

$(document).ready(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Load data from window
    if (window.evaluasiData && window.evaluasiData.evaluasiList) {
        currentData = window.evaluasiData.evaluasiList;
        console.log("Loaded evaluasi data:", currentData);
    }

    // Setup event listeners
    setupEventListeners();

    console.log("Laporan Evaluasi loaded");
});

// ========== SETUP EVENT LISTENERS ==========
function setupEventListeners() {
    // Search functionality
    $("#searchInput").on("input", function () {
        const searchTerm = $(this).val().toLowerCase().trim();

        if (searchTerm) {
            $("#btnClear").show();
            filterTable(searchTerm);
        } else {
            $("#btnClear").hide();
            showAllRows();
        }
    });

    // Clear button
    $("#btnClear").on("click", function () {
        $("#searchInput").val("");
        $(this).hide();
        showAllRows();
    });

    // Calendar button - Buka modal kalender
    $("#currentDate").on("click", function () {
        const modal = new bootstrap.Modal(
            document.getElementById("calendarModal")
        );
        modal.show();
        renderCalendar(); // Render kalender saat modal dibuka
    });

    // Calendar navigation - Navigasi bulan
    $("#prevMonth").on("click", function () {
        currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
        renderCalendar();
    });

    $("#nextMonth").on("click", function () {
        currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
        renderCalendar();
    });

    // Logout button
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").addClass("show");
    });

    $("#cancelLogout").on("click", function () {
        $("#logoutOverlay").removeClass("show");
    });

    // Close modal on overlay click
    $("#logoutOverlay").on("click", function (e) {
        if ($(e.target).hasClass("logout-overlay")) {
            $(this).removeClass("show");
        }
    });
}

// ========== FILTER TABLE ==========
function filterTable(searchTerm) {
    let visibleCount = 0;

    $("#evaluasiList tr").each(function () {
        const nama = $(this).data("nama") || "";
        
        if (nama.includes(searchTerm)) {
            $(this).show();
            visibleCount++;
        } else {
            $(this).hide();
        }
    });

    // Show/hide no results
    if (visibleCount === 0) {
        $("#noResults").show();
        $(".table-container").hide();
    } else {
        $("#noResults").hide();
        $(".table-container").show();
    }
}

function showAllRows() {
    $("#evaluasiList tr").show();
    $("#noResults").hide();
    $(".table-container").show();
}

// ========== VIEW DETAIL ==========
window.viewDetail = function (button) {
    const siswaId = $(button).data("siswa-id");
    const siswaNama = $(button).data("siswa-nama");
    const siswaNis = $(button).data("siswa-nis");

    // Show loading
    const originalText = $(button).html();
    $(button).prop("disabled", true).html('<i class="bi bi-hourglass-split"></i>');

    // Fetch detail from server
    $.ajax({
        url: "/guru/laporan-evaluasi/detail",
        type: "GET",
        data: {
            siswa_id: siswaId,
            bulan: window.evaluasiData.bulan,
            tahun: window.evaluasiData.tahun,
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
                    $("#modalPhoto").attr("src", "/assets/img/default-avatar.png");
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
                const modal = new bootstrap.Modal(document.getElementById("detailModal"));
                modal.show();
            } else {
                alert("Gagal mengambil detail perkembangan!");
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            alert("Terjadi kesalahan saat mengambil data!");
        },
        complete: function () {
            // Restore button
            $(button).prop("disabled", false).html(originalText);
        },
    });
};

// ========== CALENDAR FUNCTIONS ==========
function renderCalendar() {
    const year = currentCalendarDate.getFullYear();
    const month = currentCalendarDate.getMonth();

    const monthNames = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];
    $("#calendarMonth").text(`${monthNames[month]} ${year}`);

    const firstDay = new Date(year, month, 1).getDay();
    // Koreksi agar Senin jadi urutan pertama (sesuai header Sn Sl Rb...)
    const adjFirstDay = firstDay === 0 ? 6 : firstDay - 1;

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    const grid = $("#calendarGrid");
    grid.empty();

    // Header Hari
    const dayHeaders = ["Sn", "Sl", "Rb", "Km", "Jm", "Sb", "Mn"];
    dayHeaders.forEach((day) =>
        grid.append(`<div class="calendar-day-header">${day}</div>`)
    );

    // Tanggal bulan lalu
    for (let i = adjFirstDay - 1; i >= 0; i--) {
        grid.append(
            `<div class="calendar-day inactive">${daysInPrevMonth - i}</div>`
        );
    }

    // Tanggal bulan ini
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday =
            day === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear();
        const dayClass = isToday ? "calendar-day today" : "calendar-day";
        const dayElement = $(`<div class="${dayClass}">${day}</div>`);

        dayElement.on("click", function () {
            selectDate(year, month, day);
        });
        grid.append(dayElement);
    }
}

function selectDate(year, month, day) {
    const selected = new Date(year, month, day);
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    $("#currentDate span").text(selected.toLocaleDateString("id-ID", options));

    // Tutup Modal
    const modalElement = document.getElementById("calendarModal");
    const modal = bootstrap.Modal.getInstance(modalElement);
    modal.hide();
}

// ========== EXPORT TO EXCEL ==========
window.exportToExcel = function () {
    if (currentData.length === 0) {
        alert("Tidak ada data untuk di-export!");
        return;
    }

    // Prepare data for export
    const exportData = currentData.map((item, index) => ({
        "No": index + 1,
        "NIS": item.student_id,
        "Nama Siswa": item.student_name,
        "Tilawati": item.tilawati,
        "Kemampuan": item.kemampuan.charAt(0).toUpperCase() + item.kemampuan.slice(1),
    }));

    // Create worksheet
    const ws = XLSX.utils.json_to_sheet(exportData);
    
    // Create workbook
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Evaluasi");

    // Generate filename
    const bulan = window.evaluasiData.bulan;
    const tahun = window.evaluasiData.tahun;
    const filename = `Laporan_Evaluasi_${bulan}_${tahun}.xlsx`;

    // Download file
    XLSX.writeFile(wb, filename);

    showNotification("Data berhasil di-export!", "success");
};

// ========== PRINT REPORT ==========
window.printReport = function () {
    window.print();
};

window.printStudentReport = function () {
    const modalContent = document.getElementById("detailModal").innerHTML;
    const printWindow = window.open("", "_blank");
    
    printWindow.document.write(`
        <html>
        <head>
            <title>Laporan Perkembangan Siswa</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
            <style>
                body { padding: 20px; }
                .detail-item { margin-bottom: 15px; }
                .detail-label { font-weight: bold; }
            </style>
        </head>
        <body>
            ${modalContent}
            <script>
                window.onload = function() { 
                    window.print(); 
                    window.close(); 
                }
            </script>
        </body>
        </html>
    `);
};

// ========== SHOW NOTIFICATION ==========
function showNotification(message, type = "success") {
    // Remove existing notifications
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
    // Ctrl + F to focus search
    if (e.ctrlKey && e.key === "f") {
        e.preventDefault();
        $("#searchInput").focus();
    }

    // ESC to clear search
    if (e.key === "Escape") {
        if (!$(".modal").hasClass("show")) {
            $("#searchInput").val("").trigger("input");
            $("#searchInput").blur();
        }
    }
});