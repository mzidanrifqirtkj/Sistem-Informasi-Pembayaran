<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\PaymentValidationService;

class StorePaymentRequest extends FormRequest
{
    protected $validationService;

    public function __construct()
    {
        parent::__construct();
        $this->validationService = app(PaymentValidationService::class);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return auth()->user()->hasRole('admin');
        return auth()->check(); // Atau sesuai permission
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'santri_id' => 'required|exists:santris,id_santri',
            'nominal_pembayaran' => 'required|numeric|min:1',
            'tanggal_pembayaran' => 'nullable|date|before_or_equal:today|after:' . now()->subDays(30)->format('Y-m-d'),
            'payment_note' => 'nullable|string|max:255',
            'allocations' => 'required|array|min:1',
            'allocations.*.type' => 'required|in:bulanan,terjadwal',
            // Conditional validation untuk tagihan IDs
            'allocations.*.tagihan.id_tagihan_bulanan' => 'required_if:allocations.*.type,bulanan|exists:tagihan_bulanans,id_tagihan_bulanan',
            'allocations.*.tagihan.id_tagihan_terjadwal' => 'required_if:allocations.*.type,terjadwal|exists:tagihan_terjadwals,id_tagihan_terjadwal',
            'allocations.*.allocated_amount' => 'required|numeric|min:1',
            'sisa_pembayaran' => 'nullable|numeric|min:0'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                try {
                    // // Validate duplicate payment
                    // $this->validationService->validateDuplicatePayment(
                    //     $this->santri_id,
                    //     $this->nominal_pembayaran
                    // );

                    // Validate total allocation equals payment amount
                    $totalAllocated = collect($this->allocations)->sum('allocated_amount');
                    $sisaPembayaran = $this->sisa_pembayaran ?? 0;

                    if (($totalAllocated + $sisaPembayaran) != $this->nominal_pembayaran) {
                        $validator->errors()->add(
                            'nominal_pembayaran',
                            'Total alokasi tidak sesuai dengan nominal pembayaran'
                        );
                    }

                } catch (\Exception $e) {
                    $validator->errors()->add('nominal_pembayaran', $e->getMessage());
                }
            }
        });
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'santri_id.required' => 'Santri harus dipilih',
            'santri_id.exists' => 'Santri tidak ditemukan',
            'nominal_pembayaran.required' => 'Nominal pembayaran harus diisi',
            'nominal_pembayaran.numeric' => 'Nominal pembayaran harus berupa angka',
            'nominal_pembayaran.min' => 'Nominal pembayaran minimal Rp 1',
            'tanggal_pembayaran.before_or_equal' => 'Tanggal pembayaran tidak boleh melebihi hari ini',
            'tanggal_pembayaran.after' => 'Tanggal pembayaran tidak boleh lebih dari 30 hari yang lalu',
            'allocations.required' => 'Alokasi pembayaran harus ada',
            'allocations.array' => 'Format alokasi pembayaran tidak valid',
            'allocations.min' => 'Minimal harus ada 1 alokasi pembayaran'
        ];
    }
}
