// ========== PERKEMBANGAN PAGE SCRIPT - COMPLETE VERSION ==========

let currentFilter = "semua";
let currentSiswaId = null;
let currentDate = new Date();

$(document).ready(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Initialize
    setupEventListeners();
    initializeCalendar();

    console.log("Perkembangan page loaded");
    console.log("Total siswa:", window.totalSiswa);
    console.log("Selected date:", window.selectedDate);
});

// ========== SETUP EVENT LISTENERS ==========
function setupEventListeners() {
    // Search input
    $("#searchInput").on("input", function () {
        const searchTerm = $(this).val().toLowerCase().trim();

        if (searchTerm) {
            $("#btnClear").show();
            filterStudents(searchTerm);
        } else {
            $("#btnClear").hide();
            applyCurrentFilter();
        }
    });

    // Clear button
    $("#btnClear").on("click", function () {
        $("#searchInput").val("");
        $(this).hide();
        applyCurrentFilter();
    });

    // Filter dropdown
    $(".dropdown-item").on("click", function (e) {
        e.preventDefault();
        const filter = $(this).data("filter");
        currentFilter = filter;
        $("#filterText").text($(this).text());
        applyCurrentFilter();
    });

    // Calendar button
    $("#currentDate").on("click", function () {
        const modal = new bootstrap.Modal(
            document.getElementById("calendarModal")
        );
        modal.show();
    });

    // Calendar navigation
    $("#prevMonth").on("click", function () {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    $("#nextMonth").on("click", function () {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Save progress button
    $("#btnSaveProgress").on("click", function () {
        saveProgress();
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

// ========== CALENDAR FUNCTIONS ==========
function initializeCalendar() {
    // Set current date from window.selectedDate
    if (window.selectedDate) {
        currentDate = new Date(window.selectedDate);
    }
    renderCalendar();
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Update month title
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

    // Get first day of month and days in month
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    const grid = $("#calendarGrid");
    grid.empty();

    // Day headers
    const dayHeaders = ["Sn", "Sl", "Rb", "Km", "Jm", "Sb", "Mn"];
    dayHeaders.forEach((day) => {
        grid.append(`<div class="calendar-day-header">${day}</div>`);
    });

    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        grid.append(`<div class="calendar-day inactive">${day}</div>`);
    }

    // Current month days
    const selectedDate = new Date(window.selectedDate);
    for (let day = 1; day <= daysInMonth; day++) {
        const isSelected =
            day === selectedDate.getDate() &&
            month === selectedDate.getMonth() &&
            year === selectedDate.getFullYear();

        const dayClass = isSelected ? "calendar-day selected" : "calendar-day";
        const dayElement = $(`<div class="${dayClass}">${day}</div>`);

        // Store year, month, day in data attributes
        dayElement.data({
            year: year,
            month: month,
            day: day
        });

        dayElement.on("click", function () {
            const y = $(this).data("year");
            const m = $(this).data("month");
            const d = $(this).data("day");
            selectDate(y, m, d);
        });

        grid.append(dayElement);
    }

    // Next month days
    const totalCells = grid.children().length - 7; // Exclude headers
    const remainingCells = Math.ceil(totalCells / 7) * 7 - totalCells;

    for (let day = 1; day <= remainingCells; day++) {
        grid.append(`<div class="calendar-day inactive">${day}</div>`);
    }
}

function selectDate(year, month, day) {
    const selectedDate = new Date(year, month, day);
    
    // Format date to YYYY-MM-DD for URL
    const formattedDate = selectedDate.toISOString().split('T')[0];
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(
        document.getElementById("calendarModal")
    );
    if (modal) {
        modal.hide();
    }

    // Show loading overlay
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
    $('body').append(loadingHtml);

    // Redirect to same page with new date
    setTimeout(() => {
        window.location.href = window.location.pathname + '?tanggal=' + formattedDate;
    }, 300);
}

// ========== FILTER FUNCTIONS ==========
function filterStudents(searchTerm) {
    let visibleCount = 0;

    $(".perkembangan-table tbody tr").each(function () {
        const nama = $(this).data("nama") || "";
        const status = $(this).data("status") || "";

        const matchesSearch = nama.includes(searchTerm);
        const matchesFilter =
            currentFilter === "semua" ||
            (currentFilter === "sudah" && status === "sudah") ||
            (currentFilter === "belum" && status === "belum");

        if (matchesSearch && matchesFilter) {
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

function applyCurrentFilter() {
    let visibleCount = 0;

    $(".perkembangan-table tbody tr").each(function () {
        const status = $(this).data("status") || "";

        const matchesFilter =
            currentFilter === "semua" ||
            (currentFilter === "sudah" && status === "sudah") ||
            (currentFilter === "belum" && status === "belum");

        if (matchesFilter) {
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

// ========== INPUT/EDIT PROGRESS ==========
window.inputProgress = function (button) {
    const siswaId = $(button).data("siswa-id");
    const siswaNama = $(button).data("siswa-nama");
    const siswaNis = $(button).data("siswa-nis");

    currentSiswaId = siswaId;

    // Set modal title
    $("#modalStudentName").text(`${siswaNama} - ${siswaNis}`);
    $("#inputSiswaId").val(siswaId);

    // Clear form
    $("#progressForm")[0].reset();

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById("progressModal"));
    modal.show();
};

window.editProgress = function (button) {
    const siswaId = $(button).data("siswa-id");
    const siswaNama = $(button).data("siswa-nama");
    const siswaNis = $(button).data("siswa-nis");

    currentSiswaId = siswaId;

    // Set modal title
    $("#modalStudentName").text(`${siswaNama} - ${siswaNis}`);
    $("#inputSiswaId").val(siswaId);

    // Show loading
    const originalText = $(button).html();
    $(button)
        .prop("disabled", true)
        .html('<i class="bi bi-hourglass-split"></i>');

    // Fetch existing data
    $.ajax({
        url: "/guru/perkembangan/detail",
        type: "GET",
        data: {
            siswa_id: siswaId,
            tanggal: window.selectedDate,
        },
        success: function (response) {
            if (response.success && response.data.perkembangan) {
                const p = response.data.perkembangan;
                $("#inputTilawati").val(p.tilawati || "");
                $("#inputHalaman").val(p.halaman || "");
                $("#inputKemampuan").val(p.kemampuan || "");
                $("#inputHafalan").val(p.hafalan || "");
                $("#inputAyat").val(p.ayat || "");
                $("#inputTataKrama").val(p.tata_krama || "");
                $("#inputCatatan").val(p.catatan || "");
            } else {
                // Kalau ga ada data, clear form
                $("#progressForm")[0].reset();
            }

            // Show modal
            const modal = new bootstrap.Modal(
                document.getElementById("progressModal")
            );
            modal.show();
        },
        error: function (xhr) {
            console.error("Error:", xhr);
            alert("Gagal mengambil data perkembangan!");
        },
        complete: function () {
            // Restore button
            $(button).prop("disabled", false).html(originalText);
        },
    });
};

// ========== SAVE PROGRESS ==========
function saveProgress() {
    // Validate form
    const tilawati = $("#inputTilawati").val();
    const kemampuan = $("#inputKemampuan").val();
    const hafalan = $("#inputHafalan").val();

    if (!tilawati || !kemampuan || !hafalan) {
        alert("Mohon lengkapi semua field yang wajib diisi (*)!");
        return;
    }

    // Show loading
    const btnSave = $("#btnSaveProgress");
    const originalText = btnSave.html();
    btnSave
        .prop("disabled", true)
        .html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

    // Prepare data
    const formData = {
        siswa_id: $("#inputSiswaId").val(),
        tanggal: window.selectedDate,
        tilawati: $("#inputTilawati").val(),
        halaman: $("#inputHalaman").val(),
        kemampuan: $("#inputKemampuan").val(),
        hafalan: $("#inputHafalan").val(),
        ayat: $("#inputAyat").val(),
        tata_krama: $("#inputTataKrama").val(),
        catatan: $("#inputCatatan").val(),
    };

    // Send AJAX request
    $.ajax({
        url: "/guru/perkembangan/store",
        type: "POST",
        data: formData,
        success: function (response) {
            if (response.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(
                    document.getElementById("progressModal")
                );
                modal.hide();

                // Show success message
                showSuccessModal(response.message);

                // Reload page after 1.5 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                alert(response.message || "Gagal menyimpan perkembangan!");
            }
        },
        error: function (xhr) {
            console.error("Error:", xhr);

            let errorMessage = "Gagal menyimpan perkembangan!";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            alert(errorMessage);
        },
        complete: function () {
            // Restore button
            btnSave.prop("disabled", false).html(originalText);
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

    // Auto close after 1.5 seconds
    setTimeout(() => {
        modal.hide();
        $("#successModal").remove();
    }, 1500);
}

// ========== KEYBOARD SHORTCUTS ==========
$(document).on("keydown", function (e) {
    // Ctrl + F to focus search
    if (e.ctrlKey && e.key === "f") {
        e.preventDefault();
        $("#searchInput").focus();
    }

    // Ctrl + S to save progress (when modal is open)
    if (e.ctrlKey && e.key === "s") {
        e.preventDefault();
        if ($("#progressModal").hasClass("show")) {
            saveProgress();
        }
    }

    // ESC to clear search
    if (e.key === "Escape") {
        if (!$(".modal").hasClass("show")) {
            $("#searchInput").val("").trigger("input");
            $("#searchInput").blur();
        }
    }
});