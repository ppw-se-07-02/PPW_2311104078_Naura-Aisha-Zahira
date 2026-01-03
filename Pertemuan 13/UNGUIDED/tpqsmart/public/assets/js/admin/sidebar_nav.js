// ========== SIDEBAR NAVIGATION HANDLER - FIXED VERSION ==========
$(document).ready(function () {
    console.log("Sidebar Navigation JS loaded");

    // Initialize sidebar navigation
    initSidebarNavigation();

    // Highlight active menu based on current page
    highlightActiveMenu();

    // Add ripple effect to nav items
    addRippleEffect();

    // Handle logout modal
    setupLogoutModal();
});

// Initialize sidebar navigation
function initSidebarNavigation() {
    $(".nav-item").on("click", function (e) {
        const href = $(this).attr("href");
        const title = $(this).attr("title");
        const id = $(this).attr("id");

        // Handle logout khusus
        if (title === "Keluar" || id === "btnLogout") {
            e.preventDefault();
            $("#logoutOverlay").addClass("show");
            return;
        }

        // Untuk link lain, biarkan browser navigate normal
        if (!href || href === "#") {
            e.preventDefault();
            showComingSoonNotification("Fitur ini");
        }
    });
}

// ✅ FIXED: Highlight active menu based on current URL
function highlightActiveMenu() {
    // Get current full URL path
    const currentPath = window.location.pathname;

    console.log("Current Path:", currentPath);

    // Remove all active classes first
    $(".nav-item").removeClass("active");

    // Check each nav item
    let foundActive = false;

    $(".nav-item").each(function () {
        const href = $(this).attr("href");

        if (!href || href === "#") return;

        // Get the route from href
        const linkPath = new URL(href, window.location.origin).pathname;

        console.log("Checking:", linkPath, "vs", currentPath);

        // Exact match or starts with (for nested routes)
        if (
            currentPath === linkPath ||
            (currentPath.startsWith(linkPath) && linkPath !== "/")
        ) {
            // Only set active if not already found
            if (!foundActive) {
                $(this).addClass("active");
                foundActive = true;
                console.log(
                    "✅ Active set to:",
                    $(this).find(".nav-text").text()
                );
            }
        }
    });

    // ✅ FALLBACK: If no match found, highlight based on main section
    if (!foundActive) {
        if (
            currentPath.includes("/data-pengguna") ||
            currentPath.includes("/siswa") ||
            currentPath.includes("/guru")
        ) {
            $('a[href*="data-pengguna"]').addClass("active");
        } else if (currentPath.includes("/dashboard")) {
            $('a[href*="dashboard"]').addClass("active");
        } else if (currentPath.includes("/presensi")) {
            $('a[href*="presensi"]').addClass("active");
        } else if (currentPath.includes("/laporan")) {
            $('a[href*="laporan"]').addClass("active");
        } else if (currentPath.includes("/notifikasi")) {
            $('a[href*="notifikasi"]').addClass("active");
        }
    }
}

// Add ripple effect to nav items
function addRippleEffect() {
    $(".nav-item").on("mousedown", function (e) {
        const ripple = $('<span class="ripple"></span>');
        $(this).append(ripple);

        const x = e.pageX - $(this).offset().left;
        const y = e.pageY - $(this).offset().top;

        ripple.css({
            left: x + "px",
            top: y + "px",
        });

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
}

// Setup logout modal handlers
function setupLogoutModal() {
    // Show logout overlay
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").addClass("show");
    });

    // Cancel logout
    $("#cancelLogout").on("click", function () {
        $("#logoutOverlay").removeClass("show");
    });

    // Close on overlay click (optional)
    $("#logoutOverlay").on("click", function (e) {
        if ($(e.target).is("#logoutOverlay")) {
            $(this).removeClass("show");
        }
    });
}
