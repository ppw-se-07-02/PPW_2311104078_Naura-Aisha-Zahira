// ========== TAMBAH SISWA SCRIPT - FIXED VERSION ==========
$(document).ready(function () {
    // Setup CSRF token untuk AJAX
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Photo Upload Preview
    $("#photoUpload").on("change", function (e) {
        const file = e.target.files[0];

        if (file) {
            // Validate file type
            if (!file.type.match("image.*")) {
                alert("File harus berupa gambar (JPG, PNG, atau GIF)");
                $(this).val("");
                return;
            }

            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert("Ukuran file maksimal 2MB");
                $(this).val("");
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function (e) {
                $("#previewImage").attr("src", e.target.result).show();
                $("#uploadPlaceholder").hide();
            };
            reader.readAsDataURL(file);
        }
    });

    // Form Submission dengan AJAX
    $("#tambahSiswaForm").on("submit", function (e) {
        e.preventDefault();

        // Validasi form
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass("was-validated");
            return;
        }

        // Get form values untuk validasi
        const username = $(this).find('input[name="username"]').val();
        const password = $(this).find('input[name="password"]').val();
        const telepon = $(this).find('input[name="no_hp"]').val();

        // Validate username length
        if (username.length < 5) {
            alert("Username minimal 5 karakter!");
            $(this).find('input[name="username"]').focus();
            return;
        }

        // Validate password length
        if (password.length < 8) {
            alert("Password minimal 8 karakter!");
            $(this).find('input[name="password"]').focus();
            return;
        }

        // Validate phone number
        if (telepon && telepon.length < 10) {
            alert("Nomor handphone tidak valid! Minimal 10 digit");
            $(this).find('input[name="no_hp"]').focus();
            return;
        }

        // Show loading state
        const submitBtn = $("#btnSubmit");
        const originalText = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .html('<i class="bi bi-hourglass-split"></i> Menambahkan...');

        // Prepare FormData untuk file upload
        const formData = new FormData(this);

        // AJAX Submit
        $.ajax({
            url: $(this).attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("Success:", response);

                // Tampilkan Modal Berhasil
                showSuccessModal(formData.get("nama"));
            },
            error: function (xhr, status, error) {
                console.error("Error:", xhr.responseText);

                // Restore button
                submitBtn.prop("disabled", false).html(originalText);

                // Show error message
                let errorMessage = "Gagal menyimpan data siswa!";

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Laravel validation errors
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join("\n");
                }

                alert(errorMessage);
            },
        });
    });

    // Show Success Modal
    function showSuccessModal(namaSiswa) {
        const successModalHtml = `
            <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-body text-center py-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
                            <h5 class="mt-3 fw-bold">Berhasil!</h5>
                            <p class="text-muted">Data siswa <strong>${namaSiswa}</strong> berhasil disimpan.</p>
                            <button type="button" class="btn btn-primary px-4 mt-2" id="btnOkSuccess">OK</button>
                        </div>
                    </div>
                </div>
            </div>`;

        // Remove existing modal if any
        $("#successModal").remove();

        // Append and show
        $("body").append(successModalHtml);
        const modal = new bootstrap.Modal(
            document.getElementById("successModal")
        );
        modal.show();

        // Redirect pas klik OK
        $("#btnOkSuccess").on("click", function () {
            window.location.href = "/admin/data-pengguna";
        });
    }

    // Input Validation - Phone Number (numbers only)
    $('input[name="no_hp"]').on("input", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

    // Input Validation - ID Siswa (alphanumeric only)
    $('input[name="idSiswa"]').on("input", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, "");
    });

    // Username validation (lowercase, no spaces)
    $('input[name="username"]').on("input", function () {
        this.value = this.value.toLowerCase().replace(/\s/g, "");
    });

    // Real-time form validation feedback
    $("#tambahSiswaForm input[required], #tambahSiswaForm select[required]").on(
        "blur",
        function () {
            if ($(this).val() === "") {
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid").addClass("is-valid");
            }
        }
    );

    // Remove validation on focus
    $(
        "#tambahSiswaForm input, #tambahSiswaForm select, #tambahSiswaForm textarea"
    ).on("focus", function () {
        $(this).removeClass("is-invalid is-valid");
    });

    // Auto-generate username suggestion
    $('input[name="nama"]').on("blur", function () {
        const nama = $(this).val().trim();
        const usernameField = $('input[name="username"]');

        if (nama && !usernameField.val()) {
            // Generate username from first name
            const firstName = nama.split(" ")[0].toLowerCase();
            const randomNum = Math.floor(Math.random() * 100);
            const suggestion = firstName + randomNum;

            usernameField.attr("placeholder", `Saran: ${suggestion}`);
        }
    });

    console.log("Tambah Siswa JS loaded");
});
