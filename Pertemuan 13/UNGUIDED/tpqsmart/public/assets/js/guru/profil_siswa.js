// ========== PROFIL SISWA SCRIPT - FIXED VERSION ==========

// ✅ Ambil data dari window (sudah di-pass dari Blade)
let students = window.siswaData || [];
let filteredStudents = [...students];
let currentStudentData = null;

$(document).ready(function () {
    // Setup CSRF token
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Initialize
    setupEventListeners();
    updateTotalSiswa();

    console.log("Total siswa loaded:", students.length);
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
            filteredStudents = [...students];
            populateStudentsGrid();
            $("#noResults").hide();
        }
    });

    // Clear button
    $("#btnClear").on("click", function () {
        $("#searchInput").val("");
        $(this).hide();
        filteredStudents = [...students];
        populateStudentsGrid();
        $("#noResults").hide();
    });

    // Enter key on search
    $("#searchInput").on("keypress", function (e) {
        if (e.which === 13) {
            $(this).blur();
        }
    });

    // Student card click event (pakai delegation karena cards dinamis)
    $(document).on("click", ".student-card", function () {
        const studentId = $(this).data("student-id");
        showBiodataModal(studentId);
    });

    // Logout button
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").addClass("show");
    });

    $("#cancelLogout").on("click", function () {
        $("#logoutOverlay").removeClass("show");
    });

    // Close modal overlay on outside click
    $("#logoutOverlay").on("click", function (e) {
        if ($(e.target).hasClass("logout-overlay")) {
            $(this).removeClass("show");
        }
    });

    // Contact parent button
    $("#btnContactParent").on("click", function () {
        contactParent();
    });
}

// ========== POPULATE STUDENTS GRID ==========
function populateStudentsGrid() {
    const grid = $("#studentsGrid");
    
    // Jangan clear grid kalau data kosong - biar tampilan dari Blade tetap
    if (filteredStudents.length === 0 && students.length > 0) {
        grid.empty();
        $("#noResults").show();
        return;
    } else {
        $("#noResults").hide();
    }

    // Kalau ada filter, rebuild grid
    if (filteredStudents.length !== students.length) {
        grid.empty();

        filteredStudents.forEach((student) => {
            const col = $("<div>").addClass("col-6 col-md-4 col-lg-3");

            const card = $(`
                <div class="student-card" data-student-id="${student.id}">
                    <div class="student-photo">
                        <img src="${student.photo}" 
                             alt="${student.nama}" 
                             onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(student.nama)}&background=2EAF7D&color=fff&size=200'">
                    </div>
                    <div class="student-name">${student.nama}</div>
                    <div class="student-id">NIS: ${student.id}</div>
                </div>
            `);

            col.append(card);
            grid.append(col);
        });

        // Animate cards
        animateCards();
    }
}

// ========== ANIMATE CARDS ==========
function animateCards() {
    $(".student-card").each(function (index) {
        $(this).css({
            opacity: "0",
            transform: "translateY(20px)",
        });

        setTimeout(() => {
            $(this).css({
                opacity: "1",
                transform: "translateY(0)",
                transition: "all 0.5s ease",
            });
        }, index * 50);
    });
}

// ========== FILTER STUDENTS ==========
function filterStudents(searchTerm) {
    filteredStudents = students.filter(
        (student) =>
            student.nama.toLowerCase().includes(searchTerm) ||
            student.id.toLowerCase().includes(searchTerm)
    );

    populateStudentsGrid();
    updateTotalSiswa();
}

// ========== SHOW BIODATA MODAL ==========
function showBiodataModal(studentId) {
    const student = students.find((s) => s.id === studentId);

    if (!student) {
        console.error("Student not found:", studentId);
        return;
    }

    // Store current student
    currentStudentData = student;

    // Populate modal
    $("#modalPhoto")
        .attr("src", student.photo)
        .on("error", function () {
            $(this).attr(
                "src",
                `https://ui-avatars.com/api/?name=${encodeURIComponent(
                    student.nama
                )}&background=2EAF7D&color=fff&size=200`
            );
        });

    $("#modalNama").text(student.nama);
    $("#modalId").text(student.id);
    $("#modalGender").text(student.gender);
    $("#modalTanggalLahir").text(student.tanggalLahir);
    $("#modalUmur").text(student.umur);
    $("#modalOrangTua").text(student.orangTua);
    $("#modalWA").text(student.nomorWA);
    $("#modalAlamat").text(student.alamat);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById("biodataModal"));
    modal.show();
}

// ========== CONTACT PARENT VIA WHATSAPP ==========
function contactParent() {
    if (!currentStudentData) {
        alert("Data siswa tidak ditemukan!");
        return;
    }

    const student = currentStudentData;
    
    // Format nomor WA (hilangkan 0 di depan, tambah 62)
    let waNumber = student.nomorWA.replace(/^0/, "62");
    waNumber = waNumber.replace(/\D/g, ""); // Hapus semua non-digit

    const message = encodeURIComponent(
        `Assalamu'alaikum ${student.orangTua},\n\nSaya guru TPQ ingin berdiskusi mengenai perkembangan belajar ${student.nama}.\n\nTerima kasih.`
    );

    const whatsappUrl = `https://wa.me/${waNumber}?text=${message}`;

    window.open(whatsappUrl, "_blank");
}

// ========== UPDATE TOTAL SISWA ==========
function updateTotalSiswa() {
    $("#totalSiswa").text(filteredStudents.length);
}

// ========== EXPORT STUDENTS DATA ==========
function exportStudentsData() {
    const csv =
        "NIS,Nama,Jenis Kelamin,Tanggal Lahir,Umur,Nama Orang Tua,Nomor WA,Alamat\n" +
        students
            .map(
                (s) =>
                    `${s.id},"${s.nama}",${s.gender},"${s.tanggalLahir}",${s.umur},"${s.orangTua}",${s.nomorWA},"${s.alamat}"`
            )
            .join("\n");

    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `data_siswa_${new Date().toISOString().split("T")[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
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
        if ($("#searchInput").val()) {
            $("#searchInput").val("").trigger("input");
            $("#searchInput").blur();
        }
    }
});

// ========== GET STUDENT STATISTICS ==========
function getStudentStatistics() {
    const totalLakiLaki = students.filter(
        (s) => s.gender === "Laki-laki"
    ).length;
    const totalPerempuan = students.filter(
        (s) => s.gender === "Perempuan"
    ).length;

    return {
        total: students.length,
        lakiLaki: totalLakiLaki,
        perempuan: totalPerempuan,
    };
}

// Log statistics on load
console.log("Student Statistics:", getStudentStatistics());