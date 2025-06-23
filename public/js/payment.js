class PaymentForm {
    constructor(config) {
        this.config = config;
        this.selectedTagihan = [];
        this.nominalPembayaran = 0;
        this.previewData = null;
        this.totalTagihanTerpilih = 0;

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

        // Nominal input change with enhanced validation
        this.elements.nominalInput.on("input", () => {
            this.nominalPembayaran =
                parseFloat(this.elements.nominalInput.val()) || 0;
            this.validatePaymentAmount();
            this.updatePreviewButtons();
            this.calculateAllocation();
        });

        // Preview button
        this.elements.previewButton.on("click", () => this.showPreview());

        // Confirm payment button with overpayment handling
        this.elements.confirmButton.on("click", () =>
            this.confirmPaymentWithOptions()
        );

        // Form submission - enhanced with validation
        this.elements.paymentForm.on("submit", (e) => {
            e.preventDefault();
            this.processDirectPayment();
        });
    }

    // ENHANCED: calculateSelectedTagihan with smart auto-fill
    calculateSelectedTagihan() {
        this.selectedTagihan = [];
        this.totalTagihanTerpilih = 0;

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

            this.totalTagihanTerpilih += sisa;
        });

        // Update display
        this.elements.totalTagihanSpan.text(
            this.formatRupiah(this.totalTagihanTerpilih)
        );

        // ENHANCED AUTO-FILL: Always sync with selected tagihan
        if (this.selectedTagihan.length > 0) {
            // Auto-fill jika nominal kosong atau 0
            if (
                this.nominalPembayaran === 0 ||
                this.elements.nominalInput.val() === ""
            ) {
                this.elements.nominalInput.val(this.totalTagihanTerpilih);
                this.nominalPembayaran = this.totalTagihanTerpilih;
            }
        } else {
            // Clear nominal jika tidak ada tagihan terpilih
            this.elements.nominalInput.val("");
            this.nominalPembayaran = 0;
        }

        this.validatePaymentAmount();
        this.updatePreviewButtons();
        this.calculateAllocation();
    }

    // NEW: Enhanced payment amount validation
    validatePaymentAmount() {
        const $nominalGroup = this.elements.nominalInput.closest(".form-group");
        const $helpText = $nominalGroup.find(".form-text");

        // Remove existing validation classes
        this.elements.nominalInput.removeClass("is-invalid is-valid");
        $nominalGroup.find(".invalid-feedback").remove();

        if (this.selectedTagihan.length === 0) {
            $helpText
                .text("Pilih tagihan terlebih dahulu")
                .removeClass(
                    "text-muted text-success text-danger text-warning text-info"
                )
                .addClass("text-muted");
            return true;
        }

        if (this.nominalPembayaran <= 0) {
            this.elements.nominalInput.addClass("is-invalid");
            $nominalGroup.append(
                '<div class="invalid-feedback">Nominal pembayaran harus lebih dari 0</div>'
            );
            return false;
        }

        if (this.nominalPembayaran < this.totalTagihanTerpilih) {
            this.elements.nominalInput.addClass("is-invalid");
            $nominalGroup.append(`<div class="invalid-feedback">
                Nominal kurang dari total tagihan. Kekurangan: ${this.formatRupiah(
                    this.totalTagihanTerpilih - this.nominalPembayaran
                )}
            </div>`);
            $helpText
                .html(
                    `
                <i class="fas fa-exclamation-triangle text-warning"></i>
                Pembayaran tidak mencukupi - tagihan akan berstatus <span class="badge badge-warning">Dibayar Sebagian</span>
            `
                )
                .removeClass("text-muted text-success text-danger text-info")
                .addClass("text-warning");
            return true; // Still allow, just warn
        }

        if (this.nominalPembayaran > this.totalTagihanTerpilih) {
            this.elements.nominalInput.addClass("is-valid");
            $helpText
                .html(
                    `
                <i class="fas fa-info-circle text-info"></i>
                Kelebihan: ${this.formatRupiah(
                    this.nominalPembayaran - this.totalTagihanTerpilih
                )}
                <span class="badge badge-info">Akan dikonfirmasi saat preview</span>
            `
                )
                .removeClass("text-muted text-warning text-danger text-success")
                .addClass("text-info");
            return true;
        }

        // Exact amount
        this.elements.nominalInput.addClass("is-valid");
        $helpText
            .html(
                `
            <i class="fas fa-check-circle text-success"></i>
            Nominal sesuai dengan total tagihan
        `
            )
            .removeClass("text-muted text-warning text-danger text-info")
            .addClass("text-success");
        return true;
    }

    // ENHANCED: processDirectPayment with comprehensive validation
    processDirectPayment() {
        if (!this.validateBeforeSubmit()) return;

        const allocationResult = this.calculateProperAllocation();

        // Check overpayment
        if (allocationResult.overpayment > 0) {
            this.handleOverpaymentConfirmation(allocationResult);
            return;
        }

        // Check underpayment warning
        if (this.nominalPembayaran < this.totalTagihanTerpilih) {
            this.handleUnderpaymentConfirmation(allocationResult);
            return;
        }

        // Perfect amount - process directly
        this.submitPaymentData(
            allocationResult.allocations,
            allocationResult.overpayment
        );
    }

    // NEW: Comprehensive validation before submit
    validateBeforeSubmit() {
        if (this.selectedTagihan.length === 0) {
            Swal.fire({
                title: "Peringatan",
                text: "Pilih minimal satu tagihan",
                icon: "warning",
                backdrop: false,
            });
            return false;
        }

        if (this.nominalPembayaran <= 0) {
            Swal.fire({
                title: "Peringatan",
                text: "Masukkan nominal pembayaran",
                icon: "warning",
                backdrop: false,
            });
            this.elements.nominalInput.focus();
            return false;
        }

        return true;
    }

    // NEW: Handle underpayment confirmation
    handleUnderpaymentConfirmation(allocationResult) {
        const shortage = this.totalTagihanTerpilih - this.nominalPembayaran;

        Swal.fire({
            title: "Pembayaran Tidak Mencukupi",
            html: `
                <div class="text-left">
                    <p><strong>Total Tagihan:</strong> ${this.formatRupiah(
                        this.totalTagihanTerpilih
                    )}</p>
                    <p><strong>Nominal Pembayaran:</strong> ${this.formatRupiah(
                        this.nominalPembayaran
                    )}</p>
                    <p><strong>Kekurangan:</strong> <span class="text-danger">${this.formatRupiah(
                        shortage
                    )}</span></p>
                    <hr>
                    <p>Tagihan akan berstatus <span class="badge badge-warning">Dibayar Sebagian</span></p>
                    <p>Lanjutkan pembayaran?</p>
                </div>
            `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Lanjutkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#ffc107",
            cancelButtonColor: "#6c757d",
            backdrop: false,
        }).then((result) => {
            if (result.isConfirmed) {
                this.submitPaymentData(
                    allocationResult.allocations,
                    allocationResult.overpayment
                );
            }
        });
    }

    // ENHANCED: calculateProperAllocation
    calculateProperAllocation() {
        let remainingAmount = this.nominalPembayaran;
        let allocations = [];

        // Distribusi bertahap ke setiap tagihan yang dipilih
        for (let tagihan of this.selectedTagihan) {
            if (remainingAmount <= 0) break;

            const allocatedAmount = Math.min(remainingAmount, tagihan.sisa);

            allocations.push({
                type: tagihan.type,
                tagihan_id: tagihan.id,
                allocated_amount: allocatedAmount,
            });

            remainingAmount -= allocatedAmount;
        }

        return {
            allocations: allocations,
            overpayment: remainingAmount,
            totalAllocated: this.nominalPembayaran - remainingAmount,
        };
    }

    // ENHANCED: handleOverpaymentConfirmation
    handleOverpaymentConfirmation(allocationResult) {
        const overpaymentFormatted = this.formatRupiah(
            allocationResult.overpayment
        );

        Swal.fire({
            title: "Kelebihan Pembayaran",
            html: `
            <div class="text-left">
                <p><strong>Total Pembayaran:</strong> ${this.formatRupiah(
                    this.nominalPembayaran
                )}</p>
                <p><strong>Total Tagihan:</strong> ${this.formatRupiah(
                    allocationResult.totalAllocated
                )}</p>
                <p><strong>Kelebihan:</strong> <span class="text-danger">${overpaymentFormatted}</span></p>
                <hr>
                <p>Bagaimana dengan kelebihan pembayaran?</p>
            </div>
        `,
            icon: "question",
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: "Lanjutkan ke Bulan Berikutnya",
            denyButtonText: "Kembalikan Kelebihan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#28a745",
            denyButtonColor: "#ffc107",
            cancelButtonColor: "#6c757d",
            backdrop: false,
        }).then((result) => {
            if (result.isConfirmed) {
                // Option: Lanjutkan ke bulan berikutnya
                this.allocateToNextMonths(allocationResult);
            } else if (result.isDenied) {
                // Option: Kembalikan kelebihan (proses dengan refund)
                this.submitPaymentData(
                    allocationResult.allocations,
                    allocationResult.overpayment
                );
            }
            // Jika cancel, tidak lakukan apa-apa
        });
    }

    // ENHANCED: allocateToNextMonths
    allocateToNextMonths(allocationResult) {
        let remainingOverpayment = allocationResult.overpayment;
        let allocations = [...allocationResult.allocations];

        // Find next available tagihan
        const nextTagihan = this.findNextAvailableTagihan();

        if (nextTagihan.length > 0 && remainingOverpayment > 0) {
            let allocatedToNext = [];

            for (let tagihan of nextTagihan) {
                if (remainingOverpayment <= 0) break;

                const allocatedAmount = Math.min(
                    remainingOverpayment,
                    tagihan.sisa
                );

                allocations.push({
                    type: tagihan.type,
                    tagihan_id: tagihan.id,
                    allocated_amount: allocatedAmount,
                });

                allocatedToNext.push({
                    label: tagihan.label,
                    amount: allocatedAmount,
                });

                remainingOverpayment -= allocatedAmount;
            }

            // Show confirmation of auto-allocation
            const allocatedList = allocatedToNext
                .map(
                    (item) =>
                        `<li>${item.label}: ${this.formatRupiah(
                            item.amount
                        )}</li>`
                )
                .join("");

            Swal.fire({
                title: "Konfirmasi Alokasi Otomatis",
                html: `
                    <div class="text-left">
                        <p>Kelebihan dialokasikan ke:</p>
                        <ul>${allocatedList}</ul>
                        ${
                            remainingOverpayment > 0
                                ? `<p>Sisa kelebihan: <span class="text-warning">${this.formatRupiah(
                                      remainingOverpayment
                                  )}</span></p>`
                                : ""
                        }
                    </div>
                `,
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Ya, Proses",
                cancelButtonText: "Batal",
                backdrop: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submitPaymentData(allocations, remainingOverpayment);
                }
            });
        } else {
            // No next tagihan available
            Swal.fire({
                title: "Tidak Ada Tagihan Berikutnya",
                text: "Tidak ada tagihan lain yang tersedia. Kelebihan akan dikembalikan.",
                icon: "info",
                backdrop: false,
            }).then(() => {
                this.submitPaymentData(
                    allocationResult.allocations,
                    allocationResult.overpayment
                );
            });
        }
    }

    // ENHANCED: findNextAvailableTagihan with labels
    findNextAvailableTagihan() {
        const selectedIds = this.selectedTagihan.map((t) => t.id);
        const availableTagihan = [];

        // Find unselected bulanan tagihan
        $('.tagihan-row[data-type="bulanan"]').each((index, element) => {
            const $row = $(element);
            const id = parseInt($row.data("id"));
            const sisa = parseFloat($row.data("sisa"));

            if (!selectedIds.includes(id) && sisa > 0) {
                const bulan = $row.find("td:eq(1)").text();
                const tahun = $row.find("td:eq(2)").text();

                availableTagihan.push({
                    type: "bulanan",
                    id: id,
                    sisa: sisa,
                    label: `${bulan} ${tahun}`,
                });
            }
        });

        // Find unselected terjadwal tagihan
        $('.tagihan-row[data-type="terjadwal"]').each((index, element) => {
            const $row = $(element);
            const id = parseInt($row.data("id"));
            const sisa = parseFloat($row.data("sisa"));

            if (!selectedIds.includes(id) && sisa > 0) {
                const nama = $row.find("td:eq(1)").text();

                availableTagihan.push({
                    type: "terjadwal",
                    id: id,
                    sisa: sisa,
                    label: nama,
                });
            }
        });

        return availableTagihan;
    }

    // ENHANCED: submitPaymentData with better error handling
    submitPaymentData(allocations, sisaPembayaran) {
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

        const currentToken =
            $('meta[name="csrf-token"]').attr("content") ||
            this.config.csrfToken;

        const formData = {
            santri_id: this.config.santriId,
            nominal_pembayaran: this.nominalPembayaran,
            tanggal_pembayaran: $('[name="tanggal_pembayaran"]').val(),
            payment_note: $('[name="payment_note"]').val(),
            allocations: allocations,
            sisa_pembayaran: sisaPembayaran,
            _token: currentToken,
        };

        console.log("Submitting payment:", formData);

        $.ajax({
            url: this.config.storeUrl,
            type: "POST",
            data: formData,
            dataType: "json",
            success: (response) => {
                console.log("Payment success:", response);
                Swal.close();

                if (response.success || response.redirect_url) {
                    const redirectUrl =
                        response.redirect_url || response.redirect;
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
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
                    const redirectLocation = xhr.getResponseHeader("Location");
                    if (redirectLocation) {
                        window.location.href = redirectLocation;
                        return;
                    }
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        errorMessage = Object.values(errors).flat().join("\n");
                    } else {
                        errorMessage =
                            xhr.responseJSON?.message || "Data tidak valid";
                    }
                } else if (xhr.status === 419) {
                    errorMessage =
                        "Sesi telah berakhir. Silakan refresh halaman.";
                }

                Swal.fire({
                    title: "Error",
                    text: errorMessage,
                    icon: "error",
                    backdrop: false,
                }).then(() => {
                    if (xhr.status === 419) {
                        window.location.reload();
                    }
                });
            },
        });
    }

    // Standard allocation calculation
    calculateAllocation() {
        if (this.nominalPembayaran <= 0 || this.selectedTagihan.length === 0) {
            this.elements.allocationPreview.hide();
            return;
        }

        let sisaPembayaran = this.nominalPembayaran;
        let allocations = [];

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
        if (!this.validateBeforeSubmit()) return;

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

                        this.previewModal.modal("show");
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message || "Terjadi kesalahan",
                            icon: "error",
                            backdrop: false,
                        });
                    }
                }, 300);
            },
            error: (xhr) => {
                Swal.close();
                const message =
                    xhr.responseJSON?.message || "Terjadi kesalahan server";
                Swal.fire({
                    title: "Error",
                    text: message,
                    icon: "error",
                    backdrop: false,
                });
            },
        });
    }

    // ENHANCED: confirmPaymentWithOptions - handles overpayment options from preview modal
    confirmPaymentWithOptions() {
        if (!this.previewData) {
            console.error("No preview data available");
            return;
        }

        let overpaymentAction = "return"; // default
        const $actionRadio = $('input[name="overpayment_action"]:checked');
        if ($actionRadio.length > 0) {
            overpaymentAction = $actionRadio.val();
        }

        // Handle overpayment based on selection
        if (
            this.previewData.sisa_pembayaran > 0 &&
            overpaymentAction === "allocate"
        ) {
            this.allocateFromPreview(this.previewData);
            return;
        }

        // Normal confirmation
        this.processConfirmPayment(
            this.previewData.allocations,
            this.previewData.sisa_pembayaran
        );
    }

    // NEW: Handle allocation from preview modal
    allocateFromPreview(previewData) {
        let remainingOverpayment = previewData.sisa_pembayaran;
        let allocations = [...previewData.allocations];

        // Find next available tagihan
        const nextTagihan = this.findNextAvailableTagihan();

        if (nextTagihan.length > 0 && remainingOverpayment > 0) {
            let allocatedToNext = [];

            for (let tagihan of nextTagihan) {
                if (remainingOverpayment <= 0) break;

                const allocatedAmount = Math.min(
                    remainingOverpayment,
                    tagihan.sisa
                );

                allocations.push({
                    type: tagihan.type,
                    tagihan_id: tagihan.id,
                    allocated_amount: allocatedAmount,
                });

                allocatedToNext.push({
                    label: tagihan.label,
                    amount: allocatedAmount,
                });

                remainingOverpayment -= allocatedAmount;
            }

            // Show confirmation
            const allocatedList = allocatedToNext
                .map(
                    (item) =>
                        `<li>${item.label}: ${this.formatRupiah(
                            item.amount
                        )}</li>`
                )
                .join("");

            this.previewModal.modal("hide");

            Swal.fire({
                title: "Konfirmasi Alokasi Otomatis",
                html: `
                    <div class="text-left">
                        <p>Kelebihan dialokasikan ke:</p>
                        <ul>${allocatedList}</ul>
                        ${
                            remainingOverpayment > 0
                                ? `<p>Sisa kelebihan: <span class="text-warning">${this.formatRupiah(
                                      remainingOverpayment
                                  )}</span></p>`
                                : ""
                        }
                    </div>
                `,
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Ya, Proses",
                cancelButtonText: "Batal",
                backdrop: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.processConfirmPayment(
                        allocations,
                        remainingOverpayment
                    );
                }
            });
        } else {
            // No next tagihan available
            this.previewModal.modal("hide");
            Swal.fire({
                title: "Tidak Ada Tagihan Berikutnya",
                text: "Tidak ada tagihan lain yang tersedia. Kelebihan akan dikembalikan.",
                icon: "info",
                backdrop: false,
            }).then(() => {
                this.processConfirmPayment(
                    previewData.allocations,
                    previewData.sisa_pembayaran
                );
            });
        }
    }

    // Process confirmation payment from preview
    processConfirmPayment(allocations, sisaPembayaran) {
        this.previewModal.modal("hide");

        // Convert preview data to submission format
        const simplifiedAllocations = allocations.map((allocation) => ({
            type: allocation.type,
            tagihan_id:
                allocation.tagihan_id ||
                (allocation.type === "bulanan"
                    ? allocation.tagihan.id_tagihan_bulanan
                    : allocation.tagihan.id_tagihan_terjadwal),
            allocated_amount: allocation.allocated_amount,
        }));

        this.submitPaymentData(simplifiedAllocations, sisaPembayaran);
    }

    formatRupiah(number) {
        return "Rp " + new Intl.NumberFormat("id-ID").format(number);
    }
}

// Enhanced duplicate payment check
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
                backdrop: false,
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

// Enhanced styling
const style = document.createElement("style");
style.textContent = `
    .high-z-index-swal {
        z-index: 10000 !important;
    }

    .form-control.is-valid {
        border-color: #28a745;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
`;
document.head.appendChild(style);
