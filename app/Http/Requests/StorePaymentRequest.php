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

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'santri_id' => 'required|exists:santris,id_santri',
            'nominal_pembayaran' => 'required|numeric|min:1',
            'tanggal_pembayaran' => 'nullable|date|before_or_equal:today',
            'payment_note' => 'nullable|string|max:255',
            'allocations' => 'required|array|min:1',
            'allocations.*.type' => 'required|in:bulanan,terjadwal',
            // FIX: Sesuaikan dengan format yang dikirim frontend
            'allocations.*.tagihan_id' => 'required|integer',
            'allocations.*.allocated_amount' => 'required|numeric|min:1',
            'sisa_pembayaran' => 'nullable|numeric|min:0'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                try {
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

    public function messages(): array
    {
        return [
            'santri_id.required' => 'Santri harus dipilih',
            'santri_id.exists' => 'Santri tidak ditemukan',
            'nominal_pembayaran.required' => 'Nominal pembayaran harus diisi',
            'nominal_pembayaran.numeric' => 'Nominal pembayaran harus berupa angka',
            'nominal_pembayaran.min' => 'Nominal pembayaran minimal Rp 1',
            'allocations.required' => 'Alokasi pembayaran harus ada',
            'allocations.array' => 'Format alokasi pembayaran tidak valid',
            'allocations.min' => 'Minimal harus ada 1 alokasi pembayaran'
        ];
    }
}
