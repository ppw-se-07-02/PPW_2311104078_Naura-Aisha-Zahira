// ========== DATA PRESENSI SCRIPT - CLEAN VERSION ==========

// Global variables
let currentFilter = "all";
let currentData = [];

$(document).ready(function () {
    // Initialize data from blade
    if (window.presensiData && window.presensiData.presensiList) {
        currentData = window.presensiData.presensiList;
        renderTable();
        updateCounts();
    }

    // 1. Filter buttons click
    $(".filter-btn").on("click", function () {
        $(".filter-btn").removeClass("active");
        $(this).addClass("active");
        currentFilter = $(this).data("status");
        renderTable();
    });

    // 2. Search functionality
    $("#searchSiswa").on("input", function () {
        renderTable();
    });

    // 3. Export button
    $("#btnExport").on("click", function () {
        if (currentData.length === 0) {
            showNotification("Tidak ada data untuk di-export", "warning");
            return;
        }
        exportToCSV();
    });

    // 4. Reset button
    $("#btnReset").on("click", function () {
        if (currentData.length === 0) {
            showNotification("Tidak ada data untuk direset", "warning");
            return;
        }

        if (
            confirm("Apakah Anda yakin ingin mereset semua presensi hari ini?")
        ) {
            resetPresensi();
        }
    });

    // 5. Date change
    $("#presensiDate").on("change", function () {
        const selectedDate = $(this).val();
        console.log("Date changed to:", selectedDate);

        // TODO: Nanti reload data dari server berdasarkan tanggal
        // window.location.href = `/admin/data-presensi?date=${selectedDate}`;

        showNotification(
            "Filter tanggal akan aktif setelah database siap",
            "info"
        );
    });

    // 6. Initial counts update
    updateCounts();
});

// Render table based on current filter and search
function renderTable() {
    const searchTerm = $("#searchSiswa").val().toLowerCase();

    // Filter data
    let filteredData = currentData.filter((item) => {
        const matchesSearch =
            item.student_name?.toLowerCase().includes(searchTerm) || false;
        const matchesFilter =
            currentFilter === "all" || item.status === currentFilter;
        return matchesSearch && matchesFilter;
    });

    // Update display count
    $("#displayCount").text(filteredData.length);

    // Render table rows
    const tbody = $("#presensiTableBody");
    tbody.empty();

    if (filteredData.length === 0) {
        tbody.append(`
      <tr>
        <td colspan="5" class="text-center py-5">
          <div class="empty-table">
            <i class="bi bi-inbox"></i>
            <h5>Tidak ada data yang sesuai</h5>
            <p>${
                searchTerm
                    ? `Tidak ditemukan hasil untuk "${searchTerm}"`
                    : "Belum ada data presensi"
            }</p>
          </div>
        </td>
      </tr>
    `);
        return;
    }

    filteredData.forEach((item) => {
        const statusClass = item.status || "alpha";
        const statusText =
            statusClass.charAt(0).toUpperCase() + statusClass.slice(1);
        const waktuDisplay =
            item.waktu && item.waktu !== "-"
                ? `<i class="bi bi-clock"></i> ${item.waktu}`
                : '<span class="text-muted">-</span>';

        const row = `
      <tr data-id="${item.id}">
        <td>${item.student_id || "-"}</td>
        <td><strong>${item.student_name || "Unknown"}</strong></td>
        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
        <td><span class="waktu-presensi">${waktuDisplay}</span></td>
        <td>
          <button class="btn-edit-presensi" onclick="editPresensi(${
              item.id
          }, '${item.student_name}')">
            <i class="bi bi-pencil-fill"></i>
          </button>
        </td>
      </tr>
    `;
        tbody.append(row);
    });
}

// Update counts for each status
function updateCounts() {
    const countAll = currentData.length;
    const countHadir = currentData.filter(
        (item) => item.status === "hadir"
    ).length;
    const countIzin = currentData.filter(
        (item) => item.status === "izin"
    ).length;
    const countSakit = currentData.filter(
        (item) => item.status === "sakit"
    ).length;
    const countAlpha = currentData.filter(
        (item) => item.status === "alpha"
    ).length;

    $("#countAll").text(countAll);
    $("#countHadir").text(countHadir);
    $("#countIzin").text(countIzin);
    $("#countSakit").text(countSakit);
    $("#countAlpha").text(countAlpha);

    $("#totalCount").text(countAll);
    $("#summaryHadir").text(countHadir);
    $("#summaryIzin").text(countIzin);
    $("#summarySakit").text(countSakit);
    $("#summaryAlpha").text(countAlpha);
}

// Select class and reload data
function selectClass(classId, className) {
    console.log("Selected class:", classId, className);

    // Update UI
    $("#selectedClassName").text(className);

    // TODO: Nanti reload data dari server
    // window.location.href = `/admin/data-presensi?class=${classId}`;

    showNotification(`Memuat data kelas ${className}...`, "info");
}

// Edit presensi status (cycle through statuses)
function editPresensi(id, studentName) {
    const item = currentData.find((x) => x.id === id);
    if (!item) {
        showNotification("Data tidak ditemukan", "error");
        return;
    }

    const statuses = ["hadir", "izin", "sakit", "alpha"];
    const currentIndex = statuses.indexOf(item.status || "alpha");
    const nextIndex = (currentIndex + 1) % statuses.length;
    const newStatus = statuses[nextIndex];

    // Update status
    item.status = newStatus;
    item.waktu =
        newStatus === "hadir"
            ? new Date().toLocaleTimeString("id-ID", {
                  hour: "2-digit",
                  minute: "2-digit",
              })
            : "-";

    // Re-render table
    renderTable();
    updateCounts();

    // Show notification
    const statusText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    showNotification(
        `Status ${studentName} diubah menjadi: ${statusText}`,
        "success"
    );

    // TODO: Nanti simpan ke server via AJAX
    // savePresensi(id, newStatus);
}

// Export to CSV
function exportToCSV() {
    const date =
        $("#presensiDate").val() || new Date().toISOString().split("T")[0];
    const className = $("#selectedClassName").text() || "Unknown";

    let csv = "ID,Nama,Status,Waktu\n";

    currentData.forEach((item) => {
        const nama = (item.student_name || "").replace(/,/g, "");
        const status = item.status || "alpha";
        const waktu = item.waktu && item.waktu !== "-" ? item.waktu : "-";
        csv += `${item.student_id || "-"},${nama},${status},${waktu}\n`;
    });

    // Create and download file
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `Presensi_${className.replace(/ /g, "_")}_${date}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    showNotification("Data berhasil di-export!", "success");
}

// Reset presensi (set all to alpha)
function resetPresensi() {
    currentData.forEach((item) => {
        item.status = "alpha";
        item.waktu = "-";
    });

    renderTable();
    updateCounts();

    showNotification("Presensi berhasil direset!", "success");

    // TODO: Nanti simpan ke server via AJAX
    // resetPresensiServer();
}

// Show notification
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

// Helper: Save presensi to server (untuk nanti)
function savePresensi(id, status) {
    // TODO: Implement AJAX save
    /*
  $.ajax({
    url: '/admin/presensi/update',
    method: 'POST',
    data: {
      _token: $('meta[name="csrf-token"]').attr('content'),
      id: id,
      status: status,
      waktu: new Date().toISOString()
    },
    success: function(response) {
      console.log('Saved:', response);
    },
    error: function(error) {
      showNotification('Gagal menyimpan data', 'error');
      console.error(error);
    }
  });
  */
}

console.log("Data Presensi JS loaded");
