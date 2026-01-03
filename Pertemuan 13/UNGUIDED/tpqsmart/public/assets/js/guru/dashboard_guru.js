// ========== DASHBOARD GURU SCRIPT ==========
$(document).ready(function () {
    // Navigation Item Click
    $(".nav-item").on("click", function (e) {
        e.preventDefault();

        $(".nav-item").removeClass("active");
        $(this).addClass("active");

        const title = $(this).attr("title");

        if (title === "Keluar") {
            e.preventDefault(); // Cegah pindah halaman karena kita mau munculin popup
            $("#logoutOverlay").fadeIn(200);
            $("#logoutOverlay").addClass("show");
        } else if (title === "Presensi") {
            window.location.href = "/guru/presensi"; // Arahkan ke URL Laravel
        } else if (title === "Profil Siswa") {
            window.location.href = "/guru/profil_siswa";
        } else if (title === "Perkembangan") {
            window.location.href = "/guru/perkembangan";
        } else if (title === "Laporan Evaluasi") {
            window.location.href = "/guru/laporan_evaluasi";
        }
    });

    // ===== Logout Popup =====
    // Tampilkan popup
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").addClass("show");
    });

    // Tutup popup
    $("#cancelLogout").on("click", function () {
        $("#logoutOverlay").removeClass("show");

        $(".nav-item").removeClass("active");
    });

    // dropdown profile //
    $(".btn-dropdown").on("click", function (e) {
        e.stopPropagation();
        $(".profile-dropdown").toggle();
    });

    $(document).on("click", function () {
        $(".profile-dropdown").hide();
    });

    // Animate stat cards on load
    animateStats();

    // Update current time
    updateCurrentTime();
    setInterval(updateCurrentTime, 60000); // Update every minute
});

// ========== ANIMATION FUNCTIONS ==========

function animateStats() {
    $(".stat-card").each(function (index) {
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
        }, index * 100);
    });
}

// ========== UTILITY FUNCTIONS ==========

function updateCurrentTime() {
    const now = new Date();
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };
    const timeString = now.toLocaleDateString("id-ID", options);

    // Update time if element exists
    if ($(".current-time").length) {
        $(".current-time").text(timeString);
    }
}

// Format date for activity timestamp
function formatActivityTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) {
        const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
        if (diffHours === 0) {
            const diffMinutes = Math.floor(diffTime / (1000 * 60));
            return diffMinutes + " menit yang lalu";
        }
        return diffHours + " jam yang lalu";
    } else if (diffDays === 1) {
        return "Kemarin";
    } else {
        return diffDays + " hari yang lalu";
    }
}

// Show notification
function showNotification(message, type = "info") {
    // Create notification element
    const notification = $("<div>")
        .addClass("notification")
        .addClass("notification-" + type)
        .text(message);

    $("body").append(notification);

    // Show notification
    setTimeout(() => {
        notification.addClass("show");
    }, 100);

    // Hide and remove notification
    setTimeout(() => {
        notification.removeClass("show");
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}
