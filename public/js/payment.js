class PaymentForm {
    constructor(config) {
        this.config = config;
        this.selectedTagihan = [];
        this.nominalPembayaran = 0;
        this.previewData = null;
        this.totalTagihanTerpilih = 0;
        this.isSubmitting = false;
        this.minPayment = 20000; // Tambahkan ini

        // NEW: Track auto-fill status for smart behavior
        this.isAutoFilled = false;
        this.lastAutoFilledAmount = 0;

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

        this.previewModal = $(this.elements.previewModalElement);
        this.bindEvents();
        this.smartFilterTagihan();
        this.calculateSelectedTagihan();
    }

    // Smart filter - Hide tagihan lunas (sisa = 0)
    smartFilterTagihan() {
        $(".tagihan-row").each((index, element) => {
            const $row = $(element);
            const sisa = parseFloat($row.data("sisa"));
            const $checkbox = $row.find(".tagihan-checkbox");

            if (sisa <= 0) {
                $row.hide();
                $checkbox.prop("disabled", true);
                console.log(
                    `Hidden lunas tagihan: ${$row.find("td:eq(1)").text()} ${
                        $row.find("td:eq(2)").text() || ""
                    }`
                );
            }
        });

        this.updateHeaderTotals();
    }

    updateHeaderTotals() {
        let totalBulanan = 0;
        let totalTerjadwal = 0;

        $('.tagihan-row[data-type="bulanan"]:visible').each(
            (index, element) => {
                totalBulanan += parseFloat($(element).data("sisa"));
            }
        );

        $('.tagihan-row[data-type="terjadwal"]:visible').each(
            (index, element) => {
                totalTerjadwal += parseFloat($(element).data("sisa"));
            }
        );

        $(".tagihan-bulanan-header .total-amount").text(
            this.formatRupiah(totalBulanan)
        );
        $(".tagihan-terjadwal-header .total-amount").text(
            this.formatRupiah(totalTerjadwal)
        );
    }

    bindEvents() {
        // Checkbox changes - trigger auto-fill
        $(".tagihan-checkbox").on("change", () => {
            this.calculateSelectedTagihan();
            this.handleAutoFill(); // NEW: Auto-fill after checkbox change
        });

        // Select all - trigger auto-fill
        $("#selectAllBulanan").on("change", (e) => {
            this.selectAllBulanan(e.target.checked);
            this.handleAutoFill(); // NEW: Auto-fill after select all
        });

        $("#selectAllTerjadwal").on("change", (e) => {
            this.selectAllTerjadwal(e.target.checked);
            this.handleAutoFill(); // NEW: Auto-fill after select all
        });

        // Enhanced nominal input - track manual input
        this.elements.nominalInput.on("input", () => {
            let inputValue = parseFloat(this.elements.nominalInput.val()) || 0;

            // Force positive numbers
            if (inputValue < 0) {
                inputValue = Math.abs(inputValue);
                this.elements.nominalInput.val(inputValue);
            }

            this.nominalPembayaran = inputValue;

            // NEW: Check if this is manual input vs auto-fill
            this.detectManualInput();

            this.validatePaymentAmount();
            this.updatePreviewButtons();
            this.calculateAllocation();
        });

        // Other events
        this.elements.previewButton.on("click", () => this.showPreview());
        this.elements.confirmButton.on("click", () =>
            this.confirmPaymentWithOptions()
        );
        this.elements.paymentForm.on("submit", (e) => {
            e.preventDefault();
            this.processDirectPayment();
        });
    }

    // NEW: Detect if input is manual or auto-filled
    detectManualInput() {
        // If current input doesn't match last auto-filled amount and is not 0
        if (
            this.nominalPembayaran !== this.lastAutoFilledAmount &&
            this.nominalPembayaran !== 0
        ) {
            this.isAutoFilled = false;
            console.log("Manual input detected:", this.nominalPembayaran);
        }
    }

    // NEW: Handle auto-fill logic
    handleAutoFill() {
        // Smart Behavior (Opsi C):
        // Auto-fill only if:
        // 1. Input is 0 (empty), OR
        // 2. Input equals the last auto-filled amount (user hasn't manually changed it)

        const currentInput = this.nominalPembayaran;
        const shouldAutoFill =
            currentInput === 0 ||
            currentInput === this.lastAutoFilledAmount ||
            this.isAutoFilled;

        if (shouldAutoFill && this.totalTagihanTerpilih > 0) {
            this.autoFillNominal(this.totalTagihanTerpilih);
            console.log(
                `Auto-filled: ${this.formatRupiah(this.totalTagihanTerpilih)}`
            );
        } else if (shouldAutoFill && this.totalTagihanTerpilih === 0) {
            // Clear input if no tagihan selected and was auto-filled
            this.autoFillNominal(0);
            console.log("Auto-cleared: No tagihan selected");
        } else {
            console.log("Skip auto-fill: Manual input detected");
        }
    }

    // NEW: Auto-fill nominal input
    autoFillNominal(amount) {
        this.nominalPembayaran = amount;
        this.lastAutoFilledAmount = amount;
        this.isAutoFilled = true;

        // Update input field without triggering input event
        this.elements.nominalInput.off("input");
        this.elements.nominalInput.val(amount || "");

        // Re-bind input event
        this.elements.nominalInput.on("input", () => {
            let inputValue = parseFloat(this.elements.nominalInput.val()) || 0;

            if (inputValue < 0) {
                inputValue = Math.abs(inputValue);
                this.elements.nominalInput.val(inputValue);
            }

            this.nominalPembayaran = inputValue;
            this.detectManualInput();
            this.validatePaymentAmount();
            this.updatePreviewButtons();
            this.calculateAllocation();
        });

        // Update other UI elements
        this.validatePaymentAmount();
        this.updatePreviewButtons();
        this.calculateAllocation();
    }

    // Select All Bulanan
    selectAllBulanan(isChecked) {
        const $bulananCheckboxes = $(
            ".tagihan-row[data-type='bulanan']:visible .tagihan-checkbox"
        );
        $bulananCheckboxes.prop("checked", isChecked);
        this.calculateSelectedTagihan();
        console.log(
            `Select All Bulanan: ${isChecked ? "Checked" : "Unchecked"} ${
                $bulananCheckboxes.length
            } items`
        );
    }

    // Select All Terjadwal
    selectAllTerjadwal(isChecked) {
        const $terjadwalCheckboxes = $(
            ".tagihan-row[data-type='terjadwal']:visible .tagihan-checkbox"
        );
        $terjadwalCheckboxes.prop("checked", isChecked);
        this.calculateSelectedTagihan();
        console.log(
            `Select All Terjadwal: ${isChecked ? "Checked" : "Unchecked"} ${
                $terjadwalCheckboxes.length
            } items`
        );
    }

    // Update select all checkbox status
    updateSelectAllStatus() {
        // Update bulanan select all
        const $bulananCheckboxes = $(
            ".tagihan-row[data-type='bulanan']:visible .tagihan-checkbox"
        );
        const bulananChecked = $bulananCheckboxes.filter(":checked").length;
        const bulananTotal = $bulananCheckboxes.length;

        if (bulananTotal > 0) {
            $("#selectAllBulanan").prop(
                "checked",
                bulananChecked === bulananTotal
            );
            $("#selectAllBulanan")[0].indeterminate =
                bulananChecked > 0 && bulananChecked < bulananTotal;
        }

        // Update terjadwal select all
        const $terjadwalCheckboxes = $(
            ".tagihan-row[data-type='terjadwal']:visible .tagihan-checkbox"
        );
        const terjadwalChecked = $terjadwalCheckboxes.filter(":checked").length;
        const terjadwalTotal = $terjadwalCheckboxes.length;

        if (terjadwalTotal > 0) {
            $("#selectAllTerjadwal").prop(
                "checked",
                terjadwalChecked === terjadwalTotal
            );
            $("#selectAllTerjadwal")[0].indeterminate =
                terjadwalChecked > 0 && terjadwalChecked < terjadwalTotal;
        }
    }

    // Calculate selected tagihan
    calculateSelectedTagihan() {
        this.selectedTagihan = [];
        this.totalTagihanTerpilih = 0;

        $(".tagihan-checkbox:visible:checked").each((index, element) => {
            const $row = $(element).closest(".tagihan-row");
            const type = $row.data("type");
            const id = $row.data("id");
            const sisa = parseFloat($row.data("sisa"));

            if (sisa > 0) {
                this.selectedTagihan.push({
                    type: type,
                    id: id,
                    sisa: sisa,
                });

                this.totalTagihanTerpilih += sisa;
            }
        });

        // Update display
        this.elements.totalTagihanSpan.text(
            this.formatRupiah(this.totalTagihanTerpilih)
        );

        // Auto-update select all checkboxes
        this.updateSelectAllStatus();

        // NOTE: Auto-fill is handled separately in handleAutoFill()
        // Don't call validatePaymentAmount here to avoid infinite loop
    }

    // Enhanced validation
    validatePaymentAmount() {
        const $nominalGroup = this.elements.nominalInput.closest(".form-group");
        const $helpText = $nominalGroup.find(".form-text");

        if (!this.validateMinPayment()) {
            return false;
        }

        this.elements.nominalInput.removeClass("is-invalid is-valid");
        $nominalGroup.find(".invalid-feedback").remove();

        if (this.selectedTagihan.length === 0) {
            $helpText
                .text("Pilih tagihan terlebih dahulu")
                .removeClass()
                .addClass("form-text text-muted");
            return true;
        }

        if (this.nominalPembayaran <= 0) {
            this.elements.nominalInput.addClass("is-invalid");
            $nominalGroup.append(
                '<div class="invalid-feedback">Nominal pembayaran harus lebih dari 0</div>'
            );
            return false;
        }

        if (this.nominalPembayaran > this.totalTagihanTerpilih) {
            this.elements.nominalInput.addClass("is-valid");

            const categories = this.getSelectedCategories();
            const categoryText = categories.includes("bulanan")
                ? categories.includes("terjadwal")
                    ? "bulanan & terjadwal"
                    : "bulanan"
                : "terjadwal";

            $helpText
                .html(
                    `
                    <i class="fas fa-info-circle text-info"></i>
                    Kelebihan: ${this.formatRupiah(
                        this.nominalPembayaran - this.totalTagihanTerpilih
                    )}
                    <br><small>Akan dialokasikan otomatis ke tagihan ${categoryText} berikutnya</small>
                `
                )
                .removeClass()
                .addClass("form-text text-info");
            return true;
        }

        this.elements.nominalInput.addClass("is-valid");
        $helpText
            .html(
                `
                <i class="fas fa-check-circle text-success"></i>
                Nominal sesuai dengan tagihan terpilih
            `
            )
            .removeClass()
            .addClass("form-text text-success");
        return true;
    }

    // Get categories of selected tagihan
    getSelectedCategories() {
        const categories = new Set();
        this.selectedTagihan.forEach((tagihan) => {
            categories.add(tagihan.type);
        });
        return Array.from(categories);
    }

    // Calculate proper allocation
    calculateProperAllocation() {
        let remainingAmount = this.nominalPembayaran;
        let allocations = [];

        for (let tagihan of this.selectedTagihan) {
            if (remainingAmount <= 0) break;

            const allocatedAmount = Math.min(remainingAmount, tagihan.sisa);

            if (allocatedAmount > 0) {
                allocations.push({
                    type: tagihan.type,
                    tagihan_id: tagihan.id,
                    allocated_amount: allocatedAmount,
                });

                remainingAmount -= allocatedAmount;
            }
        }

        return {
            allocations: allocations,
            overpayment: remainingAmount,
            totalAllocated: this.nominalPembayaran - remainingAmount,
            selectedCategories: this.getSelectedCategories(),
        };
    }

    // Find next available tagihan
    findNextAvailableTagihan(priorityCategories = []) {
        const selectedIds = this.selectedTagihan.map((t) => t.id);
        const availableTagihan = [];

        if (priorityCategories.includes("bulanan")) {
            this.findNextBulananTagihan(selectedIds, availableTagihan);
        }

        if (priorityCategories.includes("terjadwal")) {
            this.findNextTerjadwalTagihan(selectedIds, availableTagihan);
        }

        return availableTagihan;
    }

    // Find next bulanan tagihan (chronological order)
    findNextBulananTagihan(selectedIds, availableTagihan) {
        const rows = $('.tagihan-row[data-type="bulanan"]:visible').toArray();

        rows.sort((a, b) => {
            const $aRow = $(a);
            const $bRow = $(b);

            const aYear = parseInt($aRow.find("td:eq(2)").text());
            const bYear = parseInt($bRow.find("td:eq(2)").text());

            if (aYear !== bYear) {
                return aYear - bYear;
            }

            const aMonth = this.getMonthOrder($aRow.find("td:eq(1)").text());
            const bMonth = this.getMonthOrder($bRow.find("td:eq(1)").text());

            return aMonth - bMonth;
        });

        rows.forEach((element) => {
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
                    order: this.getMonthOrder(bulan) + parseInt(tahun) * 12,
                });
            }
        });
    }

    // Find next terjadwal tagihan
    findNextTerjadwalTagihan(selectedIds, availableTagihan) {
        $('.tagihan-row[data-type="terjadwal"]:visible').each(
            (index, element) => {
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
                        order: index,
                    });
                }
            }
        );
    }

    // Get month order for sorting
    getMonthOrder(monthName) {
        const months = {
            Jan: 1,
            Feb: 2,
            Mar: 3,
            Apr: 4,
            May: 5,
            Jun: 6,
            Jul: 7,
            Aug: 8,
            Sep: 9,
            Oct: 10,
            Nov: 11,
            Dec: 12,
        };
        return months[monthName] || 99;
    }

    // Allocate to next months
    allocateToNextMonths(allocationResult) {
        let remainingOverpayment = allocationResult.overpayment;
        let allocations = [...allocationResult.allocations];

        const priorityCategories = allocationResult.selectedCategories || [];
        const nextTagihan = this.findNextAvailableTagihan(priorityCategories);

        nextTagihan.sort((a, b) => a.order - b.order);

        if (nextTagihan.length > 0 && remainingOverpayment > 0) {
            let allocatedToNext = [];

            for (let tagihan of nextTagihan) {
                if (remainingOverpayment <= 0) break;

                const allocatedAmount = Math.min(
                    remainingOverpayment,
                    tagihan.sisa
                );

                if (allocatedAmount > 0) {
                    allocations.push({
                        type: tagihan.type,
                        tagihan_id: tagihan.id,
                        allocated_amount: allocatedAmount,
                    });

                    allocatedToNext.push({
                        label: tagihan.label,
                        amount: allocatedAmount,
                        category: tagihan.type,
                        sisaTagihan: tagihan.sisa,
                        sisaSetelahAllokasi: tagihan.sisa - allocatedAmount,
                    });

                    remainingOverpayment -= allocatedAmount;
                }
            }

            this.showDetailedAllocationConfirmation(
                allocatedToNext,
                allocations,
                remainingOverpayment,
                priorityCategories
            );
        } else {
            const categoryText =
                priorityCategories.length > 0
                    ? `tagihan ${priorityCategories.join(" dan ")}`
                    : "tagihan";

            Swal.fire({
                title: "Tidak Ada Tagihan Tersedia",
                text: `Tidak ada ${categoryText} lain yang belum lunas. Kelebihan akan dikembalikan.`,
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

    // Enhanced overpayment confirmation
    handleOverpaymentConfirmation(allocationResult) {
        const categories = allocationResult.selectedCategories;
        const categoryText = categories.includes("bulanan")
            ? categories.includes("terjadwal")
                ? "bulanan & terjadwal"
                : "bulanan (syahriah)"
            : "terjadwal";

        Swal.fire({
            title: "Kelebihan Pembayaran Terdeteksi",
            html: `
                <div class="text-left">
                    <p><strong>Total Pembayaran:</strong> ${this.formatRupiah(
                        this.nominalPembayaran
                    )}</p>
                    <p><strong>Total Tagihan Terpilih:</strong> ${this.formatRupiah(
                        allocationResult.totalAllocated
                    )}</p>
                    <p><strong>Kelebihan:</strong> <span class="text-warning font-weight-bold">${this.formatRupiah(
                        allocationResult.overpayment
                    )}</span></p>
                    <hr>
                    <p>Bagaimana dengan kelebihan pembayaran?</p>
                    <small class="text-muted">*Alokasi otomatis akan mencari tagihan ${categoryText} berikutnya yang belum lunas</small>
                </div>
            `,
            icon: "question",
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: "Alokasikan Otomatis",
            denyButtonText: "Kembalikan Saja",
            cancelButtonText: "Batal",
            confirmButtonColor: "#28a745",
            denyButtonColor: "#ffc107",
            backdrop: false,
        }).then((result) => {
            if (result.isConfirmed) {
                this.allocateToNextMonths(allocationResult);
            } else if (result.isDenied) {
                this.submitPaymentData(
                    allocationResult.allocations,
                    allocationResult.overpayment
                );
            }
        });
    }

    // Process direct payment
    processDirectPayment() {
        if (this.isSubmitting) return;

        if (!this.validateBeforeSubmit()) return;

        const allocationResult = this.calculateProperAllocation();

        if (allocationResult.overpayment > 0) {
            this.handleOverpaymentConfirmation(allocationResult);
            return;
        }

        this.submitPaymentData(
            allocationResult.allocations,
            allocationResult.overpayment
        );
    }

    validateBeforeSubmit() {
        if (this.selectedTagihan.length === 0) {
            Swal.fire({
                title: "Peringatan",
                text: "Pilih minimal satu tagihan yang belum lunas",
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

        if (this.nominalPembayaran < this.minPayment) {
            Swal.fire({
                title: "Peringatan",
                text: `Minimal pembayaran adalah ${this.formatRupiah(
                    this.minPayment
                )}`,
                icon: "warning",
                backdrop: false,
            });
            this.elements.nominalInput.focus();
            return false;
        }

        return true;
    }

    submitPaymentData(allocations, sisaPembayaran) {
        if (this.isSubmitting) return;
        this.isSubmitting = true;

        Swal.fire({
            title: "Processing...",
            text: "Sedang memproses pembayaran...",
            allowOutsideClick: false,
            backdrop: false,
            didOpen: () => Swal.showLoading(),
        });

        const formData = {
            santri_id: this.config.santriId,
            nominal_pembayaran: this.nominalPembayaran,
            tanggal_pembayaran: $('[name="tanggal_pembayaran"]').val(),
            payment_note: $('[name="payment_note"]').val(),
            allocations: allocations,
            sisa_pembayaran: sisaPembayaran,
            _token:
                $('meta[name="csrf-token"]').attr("content") ||
                this.config.csrfToken,
        };

        console.log("Smart payment submission:", formData);

        $.ajax({
            url: this.config.storeUrl,
            type: "POST",
            data: formData,
            dataType: "json",
            success: (response) => {
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
                        text: response.message || "Terjadi kesalahan",
                        icon: "error",
                        backdrop: false,
                    });
                }
            },
            error: (xhr) => {
                Swal.close();
                this.handleSubmissionError(xhr);
            },
            complete: () => {
                this.isSubmitting = false;
            },
        });
    }

    handleSubmissionError(xhr) {
        let errorMessage = "Terjadi kesalahan saat memproses pembayaran";

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
                errorMessage = xhr.responseJSON?.message || "Data tidak valid";
            }
        } else if (xhr.status === 419) {
            errorMessage = "Sesi berakhir. Halaman akan dimuat ulang.";
            setTimeout(() => window.location.reload(), 2000);
        }

        Swal.fire({
            title: "Error",
            text: errorMessage,
            icon: "error",
            backdrop: false,
        });
    }

    // Standard allocation preview
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
                `<div class="alert alert-info py-1 px-2 mb-0">
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
            didOpen: () => Swal.showLoading(),
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
                if (response.success) {
                    this.previewData = response.data;
                    this.elements.previewModalBody.html(response.html);
                    this.previewModal.modal("show");
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.message || "Terjadi kesalahan",
                        icon: "error",
                        backdrop: false,
                    });
                }
            },
            error: (xhr) => {
                Swal.close();
                this.handleSubmissionError(xhr);
            },
        });
    }

    confirmPaymentWithOptions() {
        if (!this.previewData) return;

        let overpaymentAction = "return";
        const $actionRadio = $('input[name="overpayment_action"]:checked');
        if ($actionRadio.length > 0) {
            overpaymentAction = $actionRadio.val();
        }

        if (
            this.previewData.sisa_pembayaran > 0 &&
            overpaymentAction === "allocate"
        ) {
            this.allocateFromPreview(this.previewData);
            return;
        }

        this.processConfirmPayment(
            this.previewData.allocations,
            this.previewData.sisa_pembayaran
        );
    }

    allocateFromPreview(previewData) {
        this.previewModal.modal("hide");

        const allocationResult = {
            allocations: previewData.allocations.map((allocation) => ({
                type: allocation.type,
                tagihan_id:
                    allocation.type === "bulanan"
                        ? allocation.tagihan.id_tagihan_bulanan
                        : allocation.tagihan.id_tagihan_terjadwal,
                allocated_amount: allocation.allocated_amount,
            })),
            overpayment: previewData.sisa_pembayaran,
            selectedCategories: this.getSelectedCategories(),
        };

        this.allocateToNextMonths(allocationResult);
    }

    processConfirmPayment(allocations, sisaPembayaran) {
        this.previewModal.modal("hide");

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

    // Enhanced confirmation dengan detail alokasi
    showDetailedAllocationConfirmation(
        allocatedToNext,
        allocations,
        remainingOverpayment,
        categories
    ) {
        const categoryText = categories.includes("bulanan")
            ? categories.includes("terjadwal")
                ? "Bulanan & Terjadwal"
                : "Bulanan (Syahriah)"
            : "Terjadwal";

        let html = `<div class="text-left">
            <h6 class="text-success mb-3"><i class="fas fa-magic"></i> Alokasi Otomatis ${categoryText}</h6>
            <div class="mb-3">`;

        // Group by category untuk display yang lebih baik
        const grouped = { bulanan: [], terjadwal: [] };
        allocatedToNext.forEach((item) => {
            grouped[item.category].push(item);
        });

        Object.keys(grouped).forEach((category) => {
            if (grouped[category].length > 0) {
                const categoryName =
                    category === "bulanan" ? "Syahriah" : "Tagihan Terjadwal";
                const total = grouped[category].reduce(
                    (sum, item) => sum + item.amount,
                    0
                );

                html += `
                    <div class="mb-2">
                        <strong class="text-primary">${categoryName}:</strong>
                        <ul class="mb-1 ml-3">
                            ${grouped[category]
                                .map(
                                    (item) => `
                                <li>
                                    ${item.label}: ${this.formatRupiah(
                                        item.amount
                                    )}
                                    <br><small class="text-muted">
                                        Sisa setelah alokasi: ${this.formatRupiah(
                                            item.sisaSetelahAllokasi
                                        )}
                                        ${
                                            item.sisaSetelahAllokasi > 0
                                                ? "(Dibayar Sebagian)"
                                                : "(Lunas)"
                                        }
                                    </small>
                                </li>
                            `
                                )
                                .join("")}
                        </ul>
                        <small class="text-muted">Subtotal: ${this.formatRupiah(
                            total
                        )}</small>
                    </div>
                `;
            }
        });

        if (remainingOverpayment > 0) {
            html += `<div class="alert alert-warning py-2 mt-2">
                <strong>Sisa dikembalikan:</strong> ${this.formatRupiah(
                    remainingOverpayment
                )}
            </div>`;
        }

        html += "</div></div>";

        Swal.fire({
            title: "Konfirmasi Alokasi Otomatis",
            html: html,
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Ya, Proses Pembayaran",
            cancelButtonText: "Batal",
            backdrop: false,
            customClass: {
                popup: "swal-wide",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                this.submitPaymentData(allocations, remainingOverpayment);
            }
        });
    }

    validateMinPayment() {
        if (
            this.nominalPembayaran > 0 &&
            this.nominalPembayaran < this.minPayment
        ) {
            this.elements.nominalInput.addClass("is-invalid");
            const $nominalGroup =
                this.elements.nominalInput.closest(".form-group");
            $nominalGroup.find(".invalid-feedback").remove();
            $nominalGroup.append(
                `<div class="invalid-feedback">Minimal pembayaran adalah ${this.formatRupiah(
                    this.minPayment
                )}</div>`
            );
            return false;
        }
        return true;
    }
}

// Enhanced styling
const style = document.createElement("style");
style.textContent = `
    .swal-wide .swal2-popup {
        width: 600px !important;
        max-width: 90vw !important;
    }

    .form-control.is-valid {
        border-color: #28a745;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .tagihan-row.table-secondary {
        opacity: 0.6;
    }

    /* Enhanced checkbox styling */
    .form-check-input:indeterminate {
        background-color: #6c757d;
        border-color: #6c757d;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
    }

    /* Select all checkbox styling */
    #selectAllBulanan:indeterminate,
    #selectAllTerjadwal:indeterminate {
        opacity: 0.8;
    }

    /* Auto-fill visual feedback - subtle indication */
    .auto-filled {
        background-color: #f8f9fa !important;
        border-left: 3px solid #28a745;
        transition: all 0.3s ease;
    }
`;
document.head.appendChild(style);
