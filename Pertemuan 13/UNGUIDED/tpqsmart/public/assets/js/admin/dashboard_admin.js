// ========== ADMIN DASHBOARD SCRIPT - COMPLETE VERSION ==========
$(document).ready(function () {
    // 1. Sidebar Toggle
    $(document).on("click", ".hamburger-btn", function () {
        const sidebar = $(".sidebar");
        sidebar.toggleClass("expanded");
        const isExpanded = sidebar.hasClass("expanded");
        localStorage.setItem("sidebarExpanded", isExpanded);
    });

    // Load Sidebar State
    if (localStorage.getItem("sidebarExpanded") === "true") {
        $(".sidebar").addClass("expanded");
    }

    // 2. Mobile Menu Toggle
    $("#btnMenu").on("click", function (e) {
        e.stopPropagation();
        $("#sidebar").toggleClass("show");
    });

    // 3. Click Outside Sidebar
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".sidebar, #btnMenu, .hamburger-btn").length) {
            $("#sidebar").removeClass("show");
        }
    });

    // 4. Navigation Handler (untuk link biasa, bukan logout)
    $(".nav-item")
        .not("#btnLogout")
        .on("click", function (e) {
            const href = $(this).attr("href");

            if (!href || href === "#") {
                e.preventDefault();
                console.warn("Navigation item has invalid href:", href);
            }
            // Biarkan browser navigate untuk href yang valid
        });

    // 5. Logout Handler
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        $("#logoutOverlay").addClass("show");
    });

    // 6. Cancel Logout
    $("#cancelLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").removeClass("show");
    });

    // 7. Initialize Chart
    renderPerformanceChart();

    // 8. Animate Stats Cards
    animateStats();

    // 9. Update Time (jika ada element #currentTime)
    if ($("#currentTime").length) {
        updateCurrentTime();
        setInterval(updateCurrentTime, 60000);
    }

    // 10. Chart Filter Buttons
    $(".btn-filter").on("click", function () {
        $(".btn-filter").removeClass("active");
        $(this).addClass("active");

        const range = $(this).data("range");
        updateChartLabels(range);
    });
});

// ========== CHART FUNCTIONS ==========
function renderPerformanceChart() {
    const ctx = document.getElementById("performanceChart");
    if (!ctx) {
        console.warn("Chart canvas not found");
        return;
    }

    // Ambil data dari window.dashboardData (diset dari Blade)
    const hasData = window.dashboardData?.hasData || false;
    const labels = window.dashboardData?.chartLabels || [];
    const datasets = window.dashboardData?.chartDatasets || [];

    console.log("Chart Data:", { hasData, labels, datasets });

    // Jika tidak ada data, tampilkan chart kosong
    if (!hasData || datasets.length === 0) {
        renderEmptyChart(ctx, labels);
        return;
    }

    // Transform datasets untuk Chart.js
    const colors = ["#FF6B9D", "#9D6BFF", "#6B9DFF", "#FFB84D", "#4DFFB8"];
    const chartDatasets = datasets.map((dataset, index) => ({
        label: dataset.label,
        data: dataset.data,
        borderColor: colors[index % colors.length],
        backgroundColor: hexToRgba(colors[index % colors.length], 0.1),
        borderWidth: 3,
        tension: 0.4,
        fill: true,
        pointRadius: 0,
        pointHoverRadius: 6,
    }));

    const config = {
        type: "line",
        data: {
            labels: labels,
            datasets: chartDatasets,
        },
        options: getChartOptions(),
    };

    window.performanceChart = new Chart(ctx, config);
}

function renderEmptyChart(ctx, labels) {
    const config = {
        type: "line",
        data: {
            labels:
                labels.length > 0
                    ? labels
                    : [
                          "JAN",
                          "FEB",
                          "MAR",
                          "APR",
                          "MAY",
                          "JUN",
                          "JUL",
                          "AUG",
                          "SEP",
                          "OCT",
                          "NOV",
                          "DEC",
                      ],
            datasets: [],
        },
        options: getChartOptions(),
    };

    window.performanceChart = new Chart(ctx, config);
    console.log("Empty chart rendered");
}

function getChartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: "index",
            intersect: false,
        },
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                enabled: true,
                backgroundColor: "rgba(0, 0, 0, 0.8)",
                padding: 12,
                titleFont: { size: 13, weight: "bold" },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function (context) {
                        let label = context.dataset.label || "";
                        if (label) label += ": ";
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat("id-ID").format(
                                context.parsed.y
                            );
                        }
                        return label;
                    },
                },
            },
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    font: { size: 11 },
                    color: "#888",
                },
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: "rgba(0, 0, 0, 0.05)",
                    drawBorder: false,
                },
                ticks: {
                    font: { size: 11 },
                    color: "#888",
                    callback: function (value) {
                        return value >= 1000 ? value / 1000 + "k" : value;
                    },
                },
            },
        },
    };
}

function updateChartLabels(range) {
    if (!window.performanceChart) {
        console.warn("Chart not initialized");
        return;
    }

    let labels;
    if (range == 1) {
        labels = ["Week 1", "Week 2", "Week 3", "Week 4"];
    } else if (range == 6) {
        labels = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN"];
    } else {
        labels = [
            "JAN",
            "FEB",
            "MAR",
            "APR",
            "MAY",
            "JUN",
            "JUL",
            "AUG",
            "SEP",
            "OCT",
            "NOV",
            "DEC",
        ];
    }

    window.performanceChart.data.labels = labels;
    window.performanceChart.update();

    console.log("Chart labels updated:", labels);
}

// ========== UTILITY FUNCTIONS ==========
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

function updateCurrentTime() {
    const now = new Date();
    const options = { hour: "2-digit", minute: "2-digit", hour12: false };
    const timeString = now.toLocaleTimeString("id-ID", options);
    $("#currentTime").text(timeString);
}

function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function showComingSoonAlert(featureName) {
    const alertHtml = `
    <div class="alert alert-info alert-dismissible fade show position-fixed"
         style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;"
         role="alert">
      <i class="bi bi-info-circle-fill me-2"></i>
      <strong>${featureName}</strong> segera hadir!
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;

    $("body").append(alertHtml);

    setTimeout(() => {
        $(".alert").fadeOut(function () {
            $(this).remove();
        });
    }, 3000);
}

// Log untuk debugging
console.log("Dashboard Admin JS loaded successfully");
