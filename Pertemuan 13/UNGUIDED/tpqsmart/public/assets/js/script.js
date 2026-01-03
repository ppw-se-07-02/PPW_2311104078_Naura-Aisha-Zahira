// ========== LOGIN PAGE SCRIPT ==========

$(document).ready(function () {
    // Toggle Password Visibility
    $("#togglePassword").on("click", function () {
        const passwordInput = $("#password");
        const toggleIcon = $("#toggleIcon");
        const toggleText = $(this).find("span");

        if (passwordInput.attr("type") === "password") {
            passwordInput.attr("type", "text");
            toggleIcon.removeClass("bi-eye-slash").addClass("bi-eye");
            toggleText.text("Show");
        } else {
            passwordInput.attr("type", "password");
            toggleIcon.removeClass("bi-eye").addClass("bi-eye-slash");
            toggleText.text("Hide");
        }
    });

    $("#loginForm").on("submit", function (e) {
        // e.preventDefault(); <-- HAPUS ATAU KOMENTARKAN BARIS INI

        const email = $("#email").val();
        const password = $("#password").val();

        // Simple validation
        if (email === "" || password === "") {
            e.preventDefault(); // Hanya cegah kirim jika field kosong
            alert("Mohon isi semua field!");
            return;
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop("disabled", true).text("Memproses...");
        // Simulate API call
        // setTimeout(function () {
        //     // Redirect to dashboard
        //     window.location.href = "dashboard_guru.html";
        // }, 1000);
    });

    // Enter key support
    $("#email, #password").on("keypress", function (e) {
        if (e.which === 13) {
            $("#loginForm").submit();
        }
    });
});
