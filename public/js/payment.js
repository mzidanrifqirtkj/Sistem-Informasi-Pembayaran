class PaymentForm {
    constructor(config) {
        this.config = config;
        this.selectedTagihan = [];
        this.nominalPembayaran = 0;
        this.previewData = null;

        this.init();
    }

    init() {
        this.elements = {
            nominalInput: $("#nominalPembayaran"),
            previewButton: $("#previewButton"),
            submitButton: $("#submitButton"),
            confirmButton: $("#confirmPayment"),
            totalTagihanSpan: $("#totalTagihanTerpilih"),
            allocationPreview: $("#allocationPreview"),
            allocationList: $("#allocationList"),
            sisaPembayaran: $("#sisaPembayaran"),
            previewModalElement: document.getElementById("previewModal"),
            previewModalBody: $("#previewModalBody"),
            paymentForm: $("#paymentForm"),
        };

        // Bootstrap 4: gunakan jQuery untuk kontrol modal
        this.previewModal = $(this.elements.previewModalElement);

        this.bindEvents();
        this.calculateSelectedTagihan();
    }

    bindEvents() {
        // Checkbox changes
        $(".tagihan-checkbox").on("change", () =>
            this.calculateSelectedTagihan()
        );

        // Select all checkboxes
        $("#selectAllBulanan").on("change", (e) => {
            $(".tagihan-bulanan").prop("checked", e.target.checked);
            this.calculateSelectedTagihan();
        });

        $("#selectAllTerjadwal").on("change", (e) => {
            $(".tagihan-terjadwal").prop("checked", e.target.checked);
            this.calculateSelectedTagihan();
        });

        // Nominal input change
        this.elements.nominalInput.on("input", () => {
            this.nominalPembayaran =
                parseFloat(this.elements.nominalInput.val()) || 0;
            this.updatePreviewButtons();
            this.calculateAllocation();
        });

        // Preview button
        this.elements.previewButton.on("click", () => this.showPreview());

        // Confirm payment button
        this.elements.confirmButton.on("click", () => this.confirmPayment());

        // Prevent form submission without preview
        this.elements.paymentForm.on("submit", (e) => {
            if (!this.previewData) {
                e.preventDefault();
                this.showPreview();
            }
        });
    }

    calculateSelectedTagihan() {
        this.selectedTagihan = [];
        let totalTagihan = 0;

        $(".tagihan-checkbox:checked").each((index, element) => {
            const $row = $(element).closest(".tagihan-row");
            const type = $row.data("type");
            const id = $row.data("id");
            const sisa = parseFloat($row.data("sisa"));

            this.selectedTagihan.push({
                type: type,
                id: id,
                sisa: sisa,
            });

            totalTagihan += sisa;
        });

        // Update display
        this.elements.totalTagihanSpan.text(this.formatRupiah(totalTagihan));

        // Auto-fill nominal if empty
        if (this.elements.nominalInput.val() === "" && totalTagihan > 0) {
            this.elements.nominalInput.val(totalTagihan);
            this.nominalPembayaran = totalTagihan;
        }

        this.updatePreviewButtons();
        this.calculateAllocation();
    }

    calculateAllocation() {
        if (this.nominalPembayaran <= 0 || this.selectedTagihan.length === 0) {
            this.elements.allocationPreview.hide();
            return;
        }

        let sisaPembayaran = this.nominalPembayaran;
        let allocations = [];

        // Calculate allocations
        for (let tagihan of this.selectedTagihan) {
            if (sisaPembayaran <= 0) break;

            const allocated = Math.min(sisaPembayaran, tagihan.sisa);
            allocations.push({
                ...tagihan,
                allocated: allocated,
                remaining: tagihan.sisa - allocated,
            });

            sisaPembayaran -= allocated;
        }

        // Display allocation preview
        this.displayAllocationPreview(allocations, sisaPembayaran);
    }

    displayAllocationPreview(allocations, sisaPembayaran) {
        let html = '<ul class="list-unstyled mb-0">';

        allocations.forEach((item) => {
            const $row = $(
                `.tagihan-row[data-id="${item.id}"][data-type="${item.type}"]`
            );
            const label =
                item.type === "bulanan"
                    ? $row.find("td:eq(1)").text() +
                      " " +
                      $row.find("td:eq(2)").text()
                    : $row.find("td:eq(1)").text();

            html += `<li class="mb-1">
                <span class="text-muted">${label}:</span>
                <span class="float-right font-weight-bold">${this.formatRupiah(
                    item.allocated
                )}</span>
            </li>`;
        });

        html += "</ul>";

        this.elements.allocationList.html(html);

        // Show overpayment if any
        if (sisaPembayaran > 0) {
            this.elements.sisaPembayaran.html(
                `<div class="alert alert-warning py-1 px-2 mb-0">
                    <small><i class="fas fa-info-circle"></i> Kelebihan: ${this.formatRupiah(
                        sisaPembayaran
                    )}</small>
                </div>`
            );
        } else {
            this.elements.sisaPembayaran.html("");
        }

        this.elements.allocationPreview.show();
    }

    updatePreviewButtons() {
        const isValid =
            this.nominalPembayaran > 0 && this.selectedTagihan.length > 0;

        this.elements.previewButton.prop("disabled", !isValid);
        this.elements.submitButton.prop("disabled", !isValid);
    }

    showPreview() {
        if (this.nominalPembayaran <= 0 || this.selectedTagihan.length === 0) {
            Swal.fire({
                title: "Peringatan",
                text: "Pilih tagihan dan masukkan nominal pembayaran",
                icon: "warning",
                backdrop: false,
                customClass: {
                    container: "high-z-index-swal",
                },
            });
            return;
        }

        Swal.fire({
            title: "Loading...",
            allowOutsideClick: false,
            backdrop: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const data = {
            santri_id: this.config.santriId,
            nominal_pembayaran: this.nominalPembayaran,
            selected_tagihan: this.selectedTagihan,
            _token: this.config.csrfToken,
        };

        $.ajax({
            url: this.config.previewUrl,
            type: "POST",
            data: data,
            dataType: "json",
            success: (response) => {
                Swal.close();

                setTimeout(() => {
                    if (response.success) {
                        this.previewData = response.data;
                        this.elements.previewModalBody.html(response.html);

                        $(".swal2-container").remove();
                        $(".modal-backdrop").remove();
                        $("body").removeClass("modal-open swal2-shown");

                        // Bootstrap 4: tampilkan dengan jQuery
                        this.previewModal.modal("show");
                    } else {
                        setTimeout(() => {
                            Swal.fire({
                                title: "Error",
                                text: response.message || "Terjadi kesalahan",
                                icon: "error",
                                backdrop: false,
                            });
                        }, 100);
                    }
                }, 300);
            },
            error: (xhr) => {
                Swal.close();

                setTimeout(() => {
                    console.log("Ajax Error:", xhr); // Debug log
                    const message =
                        xhr.responseJSON?.message || "Terjadi kesalahan server";
                    Swal.fire({
                        title: "Error",
                        text: message,
                        icon: "error",
                        backdrop: false,
                    });
                }, 300);
            },
        });
    }

    confirmPayment() {
        if (!this.previewData) {
            console.error("No preview data available");
            return;
        }

        // Close modal first
        this.previewModal.modal("hide");

        // Show loading
        Swal.fire({
            title: "Processing...",
            text: "Sedang memproses pembayaran...",
            allowOutsideClick: false,
            backdrop: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        // Get current CSRF token (fresh token)
        const currentToken =
            $('meta[name="csrf-token"]').attr("content") ||
            this.config.csrfToken;

        // Simplify allocations data - hanya ambil yang diperlukan
        const simplifiedAllocations = this.previewData.allocations.map(
            (allocation) => ({
                type: allocation.type,
                tagihan: {
                    // Kirim hanya ID yang diperlukan
                    ...(allocation.type === "bulanan"
                        ? {
                              id_tagihan_bulanan:
                                  allocation.tagihan.id_tagihan_bulanan,
                          }
                        : {
                              id_tagihan_terjadwal:
                                  allocation.tagihan.id_tagihan_terjadwal,
                          }),
                },
                allocated_amount: allocation.allocated_amount,
            })
        );

        // Prepare form data sesuai dengan StorePaymentRequest
        const formData = {
            santri_id: this.config.santriId,
            nominal_pembayaran: this.nominalPembayaran,
            tanggal_pembayaran: $('[name="tanggal_pembayaran"]').val(),
            payment_note: $('[name="payment_note"]').val(),
            // Kirim allocations yang sudah disederhanakan
            allocations: simplifiedAllocations,
            sisa_pembayaran: this.previewData.sisa_pembayaran,
            _token: currentToken,
        };

        console.log("Sending payment data:", formData); // Debug log
        console.log("Preview data:", this.previewData); // Debug preview data

        // Submit menggunakan AJAX dengan error handling yang lebih baik
        $.ajax({
            url: this.config.storeUrl,
            type: "POST",
            data: formData,
            dataType: "json",
            success: (response) => {
                console.log("Payment success:", response);
                Swal.close();

                if (response.success || response.redirect) {
                    // Redirect to receipt page
                    const redirectUrl =
                        response.redirect || response.receipt_url;
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        // Fallback: reload page with success message
                        window.location.reload();
                    }
                } else {
                    Swal.fire({
                        title: "Error",
                        text:
                            response.message ||
                            "Terjadi kesalahan saat memproses pembayaran",
                        icon: "error",
                        backdrop: false,
                    });
                }
            },
            error: (xhr) => {
                Swal.close();
                console.error("Payment error:", xhr);

                let errorMessage =
                    "Terjadi kesalahan saat memproses pembayaran";

                if (xhr.status === 302) {
                    // Handle redirect case - might be successful but Laravel is redirecting
                    console.log("Received 302 redirect, following...");
                    // Check if there's a location header
                    const redirectLocation = xhr.getResponseHeader("Location");
                    if (redirectLocation) {
                        window.location.href = redirectLocation;
                        return;
                    }
                } else if (xhr.status === 422) {
                    // Validation error
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join("\n");
                        console.log("Validation errors:", errors);
                    } else {
                        errorMessage =
                            xhr.responseJSON?.message || "Data tidak valid";
                    }
                } else if (xhr.status === 419) {
                    // CSRF token mismatch
                    errorMessage =
                        "Sesi telah berakhir. Silakan refresh halaman.";
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire({
                    title: "Error",
                    text: errorMessage,
                    icon: "error",
                    backdrop: false,
                }).then(() => {
                    if (xhr.status === 419) {
                        // Refresh page on CSRF error
                        window.location.reload();
                    }
                });
            },
        });
    }

    formatRupiah(number) {
        return "Rp " + new Intl.NumberFormat("id-ID").format(number);
    }
}

// Check for duplicate payment warning
function checkDuplicatePayment(santriId, nominal, callback) {
    const lastPaymentKey = `lastPayment_${santriId}`;
    const lastPayment = localStorage.getItem(lastPaymentKey);

    if (lastPayment) {
        const data = JSON.parse(lastPayment);
        const timeDiff = Date.now() - data.timestamp;
        const fiveMinutes = 5 * 60 * 1000;

        if (timeDiff < fiveMinutes && data.nominal === nominal) {
            Swal.fire({
                title: "Peringatan!",
                text: "Pembayaran dengan nominal yang sama terdeteksi dalam 5 menit terakhir. Lanjutkan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Lanjutkan",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    callback(true);
                } else {
                    callback(false);
                }
            });
            return;
        }
    }

    // Save current payment info
    localStorage.setItem(
        lastPaymentKey,
        JSON.stringify({
            nominal: nominal,
            timestamp: Date.now(),
        })
    );

    callback(true);
}

const style = document.createElement("style");
style.textContent = `
    .high-z-index-swal {
        z-index: 10000 !important;
    }
`;
document.head.appendChild(style);
