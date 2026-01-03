// ========== RIWAYAT NOTIFIKASI SCRIPT - CLEAN VERSION ==========

// Global variables
let currentData = [];
let filterPenerima = "all";
let filterStatus = "all";

$(document).ready(function () {
    // Initialize data from blade
    if (window.notifikasiData && window.notifikasiData.notifikasiList) {
        currentData = window.notifikasiData.notifikasiList;
        renderTable();
        updateCounts();
    }

    // 1. Filter chips click
    $(".filter-chip").on("click", function () {
        const filterType = $(this).data("filter");
        const filterValue = $(this).data("value");

        // Update active state for this filter group
        $(this).siblings(`[data-filter="${filterType}"]`).removeClass("active");
        $(this).addClass("active");

        // Update filter variables
        if (filterType === "penerima") {
            filterPenerima = filterValue;
        } else if (filterType === "status") {
            filterStatus = filterValue;
        }

        renderTable();
    });

    // 2. Search functionality
    $("#searchNotifikasi").on("input", function () {
        renderTable();
    });

    // 3. Button Kirim Pesan Baru
    $("#btnKirimBaru").on("click", function () {
        $("#formKirimPesan")[0].reset();
        const modal = new bootstrap.Modal(
            document.getElementById("kirimPesanModal")
        );
        modal.show();
    });

    // 4. Form Kirim Pesan
    $("#btnKirim").on("click", function () {
        const penerima = $("#penerima").val();
        const pesan = $("#pesan").val();

        if (!penerima || !pesan) {
            showNotification("Mohon lengkapi semua field!", "warning");
            return;
        }

        if (pesan.length > 500) {
            showNotification("Pesan maksimal 500 karakter!", "warning");
            return;
        }

        // Add new notification
        const newNotif = {
            id: currentData.length + 1,
            tanggal: new Date().toISOString(),
            penerima: penerima,
            status: "berhasil",
            pesan: pesan,
        };

        currentData.unshift(newNotif);

        // Close modal
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("kirimPesanModal")
        );
        modal.hide();

        // Reset form
        $("#formKirimPesan")[0].reset();

        // Update table
        renderTable();
        updateCounts();
        updateStats();

        showNotification(
            "Pesan berhasil dikirim ke " + penerima + "!",
            "success"
        );
    });

    // 5. Button Kirim Ulang Semua
    $("#btnKirimUlang").on("click", function () {
        const gagalCount = currentData.filter(
            (item) => item.status === "gagal"
        ).length;

        if (gagalCount === 0) {
            showNotification("Tidak ada pesan yang gagal", "info");
            return;
        }

        if (confirm(`Kirim ulang ${gagalCount} pesan yang gagal?`)) {
            // Update all gagal to berhasil
            currentData.forEach((item) => {
                if (item.status === "gagal") {
                    item.status = "berhasil";
                }
            });

            renderTable();
            updateCounts();
            updateStats();

            showNotification(
                `${gagalCount} pesan berhasil dikirim ulang!`,
                "success"
            );
        }
    });

    // 6. Button Export
    $("#btnExport").on("click", function () {
        if (currentData.length === 0) {
            showNotification("Tidak ada data untuk di-export", "warning");
            return;
        }
        exportToCSV();
    });

    // 7. Initial counts update
    updateCounts();
    updateStats();
});

// Render table based on current filter and search
function renderTable() {
    const searchTerm = $("#searchNotifikasi").val().toLowerCase();

    // Filter data
    let filteredData = currentData.filter((item) => {
        const matchesSearch =
            item.pesan?.toLowerCase().includes(searchTerm) ||
            item.penerima?.toLowerCase().includes(searchTerm) ||
            false;
        const matchesPenerima =
            filterPenerima === "all" || item.penerima === filterPenerima;
        const matchesStatus =
            filterStatus === "all" || item.status === filterStatus;

        return matchesSearch && matchesPenerima && matchesStatus;
    });

    // Update display count
    $("#displayCount").text(filteredData.length);

    // Render table rows
    const tbody = $("#notifikasiTableBody");
    tbody.empty();

    if (filteredData.length === 0) {
        tbody.append(`
      <tr>
        <td colspan="6" class="text-center py-5">
          <div class="empty-table">
            <i class="bi bi-inbox"></i>
            <h5>Tidak ada data yang sesuai</h5>
            <p>${
                searchTerm
                    ? `Tidak ditemukan hasil untuk "${searchTerm}"`
                    : "Belum ada data notifikasi"
            }</p>
          </div>
        </td>
      </tr>
    `);
        return;
    }

    filteredData.forEach((item, index) => {
        const statusClass = item.status || "gagal";
        const statusText = statusClass === "berhasil" ? "Berhasil" : "Gagal";
        const statusIcon =
            statusClass === "berhasil" ? "check-circle-fill" : "x-circle-fill";
        const resendDisabled = statusClass === "berhasil" ? "disabled" : "";

        // Format date
        let tanggal = "-";
        if (item.tanggal) {
            try {
                const date = new Date(item.tanggal);
                tanggal = date.toLocaleDateString("id-ID", {
                    day: "2-digit",
                    month: "2-digit",
                    year: "2-digit",
                });
            } catch (e) {
                tanggal = item.tanggal;
            }
        }

        const row = `
      <tr data-id="${item.id}">
        <td>${index + 1}</td>
        <td>${tanggal}</td>
        <td><strong>${item.penerima || "-"}</strong></td>
        <td>
          <span class="status-badge ${statusClass}">
            <i class="bi bi-${statusIcon}"></i> ${statusText}
          </span>
        </td>
        <td><span class="pesan-text" title="${item.pesan || "-"}">${
            item.pesan || "-"
        }</span></td>
        <td>
          <div class="action-btns">
            <button class="btn-action view" onclick="viewDetail(${
                item.id
            })" title="Lihat Detail">
              <i class="bi bi-eye-fill"></i>
            </button>
            <button class="btn-action resend" onclick="resendNotif(${
                item.id
            })" title="Kirim Ulang" ${resendDisabled}>
              <i class="bi bi-arrow-clockwise"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
        tbody.append(row);
    });
}

// Update counts for summary
function updateCounts() {
    const totalBerhasil = currentData.filter(
        (item) => item.status === "berhasil"
    ).length;
    const totalGagal = currentData.filter(
        (item) => item.status === "gagal"
    ).length;
    const total = currentData.length;

    $("#summaryBerhasil").text(totalBerhasil);
    $("#summaryGagal").text(totalGagal);
    $("#countGagal").text(totalGagal);
    $("#totalCount").text(total);
}

// Update stat cards
function updateStats() {
    const totalBerhasil = currentData.filter(
        (item) => item.status === "berhasil"
    ).length;
    const totalGagal = currentData.filter(
        (item) => item.status === "gagal"
    ).length;
    const total = currentData.length;

    // Count today's notifications
    const today = new Date().toDateString();
    const hariIni = currentData.filter((item) => {
        const itemDate = new Date(item.tanggal).toDateString();
        return itemDate === today;
    }).length;

    $("#totalBerhasil").text(totalBerhasil);
    $("#totalGagal").text(totalGagal);
    $("#totalPesan").text(total);
    $("#totalHariIni").text(hariIni);
}

// View detail
function viewDetail(id) {
    const item = currentData.find((x) => x.id === id);
    if (!item) {
        showNotification("Data tidak ditemukan", "error");
        return;
    }

    const statusClass =
        item.status === "berhasil" ? "text-success" : "text-danger";
    const statusIcon =
        item.status === "berhasil" ? "check-circle-fill" : "x-circle-fill";
    const statusText = item.status === "berhasil" ? "Berhasil" : "Gagal";

    // Format date
    let tanggalFormatted = "-";
    if (item.tanggal) {
        try {
            const date = new Date(item.tanggal);
            tanggalFormatted = date.toLocaleDateString("id-ID", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            });
        } catch (e) {
            tanggalFormatted = item.tanggal;
        }
    }

    const content = `
    <div class="detail-item">
      <div class="detail-label">Tanggal Kirim</div>
      <div class="detail-value">${tanggalFormatted}</div>
    </div>
    <div class="detail-item">
      <div class="detail-label">Penerima</div>
      <div class="detail-value"><strong>${item.penerima || "-"}</strong></div>
    </div>
    <div class="detail-item">
      <div class="detail-label">Status Pengiriman</div>
      <div class="detail-value ${statusClass}">
        <strong><i class="bi bi-${statusIcon}"></i> ${statusText}</strong>
      </div>
    </div>
    <div class="detail-item">
      <div class="detail-label">Isi Pesan</div>
      <div class="detail-value">${item.pesan || "-"}</div>
    </div>
  `;

    $("#detailContent").html(content);

    const modal = new bootstrap.Modal(
        document.getElementById("viewDetailModal")
    );
    modal.show();
}

// Resend notification
function resendNotif(id) {
    const item = currentData.find((x) => x.id === id);
    if (!item || item.status === "berhasil") {
        showNotification("Notifikasi sudah berhasil dikirim", "info");
        return;
    }

    if (confirm(`Kirim ulang notifikasi ke ${item.penerima}?`)) {
        item.status = "berhasil";

        renderTable();
        updateCounts();
        updateStats();

        showNotification(
            `Notifikasi berhasil dikirim ulang ke ${item.penerima}!`,
            "success"
        );

        // TODO: Nanti simpan ke server via AJAX
        // saveNotifikasiToServer(id, item);
    }
}

// Export to CSV
function exportToCSV() {
    let csv = "No,Tanggal,Penerima,Status,Pesan\n";

    currentData.forEach((item, index) => {
        const pesan = (item.pesan || "").replace(/,/g, ";").replace(/\n/g, " ");
        const penerima = (item.penerima || "").replace(/,/g, "");
        const status = item.status || "gagal";

        let tanggal = "-";
        if (item.tanggal) {
            try {
                const date = new Date(item.tanggal);
                tanggal = date.toLocaleDateString("id-ID");
            } catch (e) {
                tanggal = item.tanggal;
            }
        }

        csv += `${index + 1},${tanggal},${penerima},${status},"${pesan}"\n`;
    });

    // Create and download file
    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `Riwayat_Notifikasi_${new Date()
        .toLocaleDateString("id-ID")
        .replace(/\//g, "-")}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    showNotification("Data berhasil di-export!", "success");
}

// Show notification toast
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
         style="top: 90px; right: 20px; z-index: 9999; min-width: 320px; max-width: 400px; box-shadow: 0 8px 24px rgba(0,0,0,0.2);" 
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
    }, 3500);
}

// Helper: Save to server (untuk nanti)
function saveNotifikasiToServer(id, data) {
    // TODO: Implement AJAX save
    /*
  $.ajax({
    url: '/admin/notifikasi/update',
    method: 'POST',
    data: {
      _token: $('meta[name="csrf-token"]').attr('content'),
      id: id,
      status: data.status
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

console.log("Riwayat Notifikasi JS loaded");
