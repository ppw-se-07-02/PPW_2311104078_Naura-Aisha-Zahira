// ========== EDIT GURU SCRIPT ==========
$(document).ready(function () {
    // Setup CSRF token
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
                $("#previewImage").attr("src", e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Live update display nama dan ID/Kelas
    $('input[name="nama"]').on("input", function () {
        $("#displayName").text($(this).val() || "Nama Guru");
    });

    $('input[name="idGuru"], select[name="kelas"]').on("change", function () {
        const idGuru = $('input[name="idGuru"]').val();
        const kelas = $('select[name="kelas"]').val();
        $("#displayIdKelas").text(idGuru + " / Kelas " + kelas);
    });

    // Form Validation
    $('input[name="no_hp"]').on("input", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

    $('input[name="idGuru"]').on("input", function () {
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, "");
    });

    $('input[name="username"]').on("input", function () {
        this.value = this.value.toLowerCase().replace(/\s/g, "");
    });

    // Form Submission
    $("#editGuruForm").on("submit", function (e) {
        e.preventDefault();

        // Validate
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass("was-validated");
            return;
        }

        const username = $('input[name="username"]').val();
        const password = $('input[name="password"]').val();

        if (username.length < 5) {
            alert("Username minimal 5 karakter!");
            $('input[name="username"]').focus();
            return;
        }

        if (password && password.length < 8) {
            alert("Password minimal 8 karakter!");
            $('input[name="password"]').focus();
            return;
        }

        // Show loading
        const submitBtn = $("#btnSubmit");
        const originalText = submitBtn.html();
        submitBtn
            .prop("disabled", true)
            .addClass("btn-loading")
            .html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

        // Prepare FormData
        const formData = new FormData(this);

        // AJAX Submit
        $.ajax({
            url: $(this).attr("action") || window.location.href,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    showSuccessModal();
                }
            },
            error: function (xhr) {
                submitBtn
                    .prop("disabled", false)
                    .removeClass("btn-loading")
                    .html(originalText);

                let errorMessage = xhr.responseJSON
                    ? xhr.responseJSON.message
                    : "Terjadi kesalahan";

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join("\n");
                }

                alert(errorMessage);
            },
        });
    });

    // Success Modal & Redirect
    function showSuccessModal() {
        const successHtml = `
      <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content" style="border-radius: 20px; border: none; padding: 20px;">
            <div class="modal-body text-center">
              <div class="mb-3">
                <i class="bi bi-check-circle-fill" style="font-size: 4rem; color: #2eaf7d;"></i>
              </div>
              <h4 class="fw-bold">Berhasil!</h4>
              <p class="text-muted">Data guru telah diperbarui.</p>
              <button type="button" class="btn btn-primary w-100" id="btnOkSuccess">OK</button>
            </div>
          </div>
        </div>
      </div>`;

        $("body").append(successHtml);
        const myModal = new bootstrap.Modal(
            document.getElementById("successModal")
        );
        myModal.show();

        $("#btnOkSuccess").on("click", function () {
            // Redirect ke detail guru
            const guruId = window.location.pathname.split("/")[3]; // Ambil ID dari URL
            window.location.href = `/admin/guru/${guruId}`;
        });
    }

    // Logout Logic
    $("#btnLogout").on("click", function (e) {
        e.preventDefault();
        $("#logoutOverlay").addClass("show");
    });

    $("#cancelLogout").on("click", function () {
        $("#logoutOverlay").removeClass("show");
    });

    console.log("Edit Guru JS loaded");
});