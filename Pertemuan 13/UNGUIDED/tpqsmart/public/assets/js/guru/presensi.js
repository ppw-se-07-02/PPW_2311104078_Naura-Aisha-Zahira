// ========== PRESENSI PAGE SCRIPT - FIXED VERSION ==========

$(document).ready(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Initialize
    setupEventListeners();
    updateCount();

    console.log("Presensi page loaded");
    console.log("Total siswa:", window.totalSiswa);
});

// ========== SETUP EVENT LISTENERS ==========
function setupEventListeners() {
    // Radio button change event
    $('input[type="radio"]').on("change", function () {
        updateCount();
        updateCheckAllStatus();
    });

    // Check all functionality
    $("#checkAll").on("change", function () {
        const isChecked = $(this).is(":checked");

        if (isChecked) {
            $('input[type="radio"][value="hadir"]').prop("checked", true);
        } else {
            $('input[type="radio"]').prop("checked", false);
        }

        updateCount();
    });

    // Submit button
    $("#btnSubmit").on("click", function () {
        submitPresensi();
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

// ========== UPDATE COUNT ==========
function updateCount() {
    let hadir = 0,
        izin = 0,
        sakit = 0,
        alpha = 0;

    // Count checked radios
    $('input[type="radio"]:checked').each(function () {
        const value = $(this).val();
        switch (value) {
            case "hadir":
                hadir++;
                break;
            case "izin":
                izin++;
                break;
            case "sakit":
                sakit++;
                break;
            case "alpha":
                alpha++;
                break;
        }
    });

    // Count unchecked as alpha
    const totalChecked = hadir + izin + sakit + alpha;
    const totalSiswa = window.totalSiswa || 0;
    alpha += totalSiswa - totalChecked;

    // Update UI with animation
    animateCounter($("#countHadir"), hadir);
    animateCounter($("#countIzin"), izin);
    animateCounter($("#countSakit"), sakit);
    animateCounter($("#countAlfa"), alpha);
}

// ========== ANIMATE COUNTER ==========
function animateCounter(element, targetValue) {
    const currentValue = parseInt(element.text()) || 0;

    if (currentValue !== targetValue) {
        element.css("transform", "scale(1.2)");
        element.text(targetValue);

        setTimeout(() => {
            element.css("transform", "scale(1)");
        }, 200);
    }
}

// ========== UPDATE CHECK ALL STATUS ==========
function updateCheckAllStatus() {
    const totalRadios = $('input[type="radio"][name^="attendance_"]').length / 4; // 4 options per student
    const totalHadir = $('input[type="radio"][value="hadir"]:checked').length;

    $("#checkAll").prop("checked", totalHadir === totalRadios && totalRadios > 0);
}

// ========== SUBMIT PRESENSI ==========
function submitPresensi() {
    const presensiData = [];
    const allRadios = {};

    // Collect all radio buttons grouped by student
    $('input[type="radio"]').each(function () {
        const name = $(this).attr("name");
        const siswaId = $(this).data("siswa-id");

        if (!allRadios[siswaId]) {
            allRadios[siswaId] = {
                siswa_id: siswaId,
                checked: false,
            };
        }

        if ($(this).is(":checked")) {
            allRadios[siswaId].status = $(this).val();
            allRadios[siswaId].checked = true;
        }
    });

    // Convert to array and set alpha for unchecked
    Object.values(allRadios).forEach((data) => {
        presensiData.push({
            siswa_id: data.siswa_id,
            status: data.checked ? data.status : "alpha",
            keterangan: null,
        });
    });

    // Validation
    if (presensiData.length === 0) {
        showAlert("error", "Tidak ada data presensi!");
        return;
    }

    // Show loading
    const btnSubmit = $("#btnSubmit");
    const originalText = btnSubmit.html();
    btnSubmit
        .prop("disabled", true)
        .html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

    // Send AJAX request
    $.ajax({
        url: "{{ route('guru.presensi.store') }}",
        type: "POST",
        data: {
            tanggal: window.selectedDate,
            presensi: presensiData,
        },
        success: function (response) {
            if (response.success) {
                // Update stats
                if (response.stats) {
                    $("#countHadir").text(response.stats.hadir);
                    $("#countIzin").text(response.stats.izin);
                    $("#countSakit").text(response.stats.sakit);
                    $("#countAlfa").text(response.stats.alpha);
                }

                showSuccessModal(response.message);
            } else {
                showAlert("error", response.message || "Gagal menyimpan presensi!");
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr);

            let errorMessage = "Gagal menyimpan presensi!";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            showAlert("error", errorMessage);
        },
        complete: function () {
            // Restore button
            btnSubmit.prop("disabled", false).html(originalText);
        },
    });
}

// ========== SHOW SUCCESS MODAL ==========
function showSuccessModal(message) {
    const modalHtml = `
    <div class="modal fade" id="successModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
          <div class="modal-body text-center p-4">
            <div class="mb-3">
              <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #2eaf7d;"></i>
            </div>
            <h4 class="fw-bold">Berhasil!</h4>
            <p class="text-muted">${message}</p>
            <button type="button" class="btn btn-primary w-100 mt-3" id="btnOkSuccess" style="background: linear-gradient(135deg, #2eaf7d, #83d1a7); border: none;">OK</button>
          </div>
        </div>
      </div>
    </div>`;

    // Remove existing modal
    $("#successModal").remove();

    // Add and show
    $("body").append(modalHtml);
    const modal = new bootstrap.Modal(document.getElementById("successModal"));
    modal.show();

    // Close on OK
    $("#btnOkSuccess").on("click", function () {
        modal.hide();
        // Optional: reload page
        // window.location.reload();
    });
}

// ========== SHOW ALERT ==========
function showAlert(type, message) {
    alert(message);

    // Alternative: Bootstrap alert
    // const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    // const alertHtml = `
    //   <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
    //     ${message}
    //     <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    //   </div>
    // `;
    // $('.presensi-card').prepend(alertHtml);
}

// ========== KEYBOARD SHORTCUTS ==========
$(document).on("keydown", function (e) {
    // Ctrl + S to save
    if (e.ctrlKey && e.key === "s") {
        e.preventDefault();
        submitPresensi();
    }

    // Ctrl + A to check all
    if (e.ctrlKey && e.key === "a") {
        e.preventDefault();
        $("#checkAll").prop("checked", true).trigger("change");
    }
});

// ========== GET ATTENDANCE DATA (for export) ==========
function getAttendanceData() {
    const data = [];

    $('input[type="radio"]:checked').each(function () {
        const siswaId = $(this).data("siswa-id");
        const siswaName = $(this)
            .closest("tr")
            .find("td:nth-child(2)")
            .text();

        data.push({
            siswa_id: siswaId,
            nama: siswaName,
            status: $(this).val(),
            tanggal: window.selectedDate,
        });
    });

    return data;
}

// ========== EXPORT TO CSV (Optional) ==========
function exportToCSV() {
    const data = getAttendanceData();

    if (data.length === 0) {
        showAlert("error", "Tidak ada data untuk diekspor!");
        return;
    }

    let csv = "Siswa ID,Nama,Status,Tanggal\n";
    data.forEach((row) => {
        csv += `${row.siswa_id},"${row.nama}",${row.status},${row.tanggal}\n`;
    });

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `presensi_${window.selectedDate}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}