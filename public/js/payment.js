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
        if (!this.previewData) return;

        // Prepare form data
        const formData = {
            santri_id: this.config.santriId,
            nominal_pembayaran: this.nominalPembayaran,
            tanggal_pembayaran: $('[name="tanggal_pembayaran"]').val(),
            payment_note: $('[name="payment_note"]').val(),
            allocations: this.previewData.allocations,
            sisa_pembayaran: this.previewData.sisa_pembayaran,
        };

        // Add CSRF token
        const form = $("<form>", {
            action: this.config.storeUrl,
            method: "POST",
        });

        // Add hidden inputs
        form.append(
            $("<input>", {
                type: "hidden",
                name: "_token",
                value: this.config.csrfToken,
            })
        );

        // Add form data as JSON
        form.append(
            $("<input>", {
                type: "hidden",
                name: "payment_data",
                value: JSON.stringify(formData),
            })
        );

        // Submit form
        $("body").append(form);
        form.submit();
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

const style = document.createElement("css_inline");
style.textContent = `
    .high-z-index-swal {
        z-index: 10000 !important;
    }
`;
document.head.appendChild(style);
