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
            'allocations.*.tagihan_id' => 'required|integer',
            'allocations.*.allocated_amount' => 'required|numeric|min:1', // CRITICAL: min:1
            'sisa_pembayaran' => 'nullable|numeric|min:0'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                try {
                    // Validate allocation amounts
                    foreach ($this->allocations as $index => $allocation) {
                        // CRITICAL: Ensure no zero or negative allocations
                        if ($allocation['allocated_amount'] <= 0) {
                            $validator->errors()->add(
                                "allocations.{$index}.allocated_amount",
                                'Jumlah alokasi harus lebih dari 0'
                            );
                        }

                        // Validate tagihan exists and has sufficient sisa
                        $tagihan = $this->getTagihan($allocation['type'], $allocation['tagihan_id']);
                        if (!$tagihan) {
                            $validator->errors()->add(
                                "allocations.{$index}.tagihan_id",
                                'Tagihan tidak ditemukan'
                            );
                        } elseif ($tagihan->sisa_tagihan <= 0) {
                            $validator->errors()->add(
                                "allocations.{$index}.tagihan_id",
                                'Tagihan sudah lunas'
                            );
                        } elseif ($allocation['allocated_amount'] > $tagihan->sisa_tagihan) {
                            $validator->errors()->add(
                                "allocations.{$index}.allocated_amount",
                                'Jumlah alokasi melebihi sisa tagihan'
                            );
                        }
                    }

                    // Validate total allocation + sisa = nominal pembayaran
                    $totalAllocated = collect($this->allocations)->sum('allocated_amount');
                    $sisaPembayaran = $this->sisa_pembayaran ?? 0;

                    if (($totalAllocated + $sisaPembayaran) != $this->nominal_pembayaran) {
                        $validator->errors()->add(
                            'nominal_pembayaran',
                            'Total alokasi + sisa tidak sesuai dengan nominal pembayaran'
                        );
                    }

                } catch (\Exception $e) {
                    $validator->errors()->add('general', $e->getMessage());
                }
            }
        });
    }

    protected function getTagihan($type, $id)
    {
        if ($type === 'bulanan') {
            return \App\Models\TagihanBulanan::find($id);
        } elseif ($type === 'terjadwal') {
            return \App\Models\TagihanTerjadwal::find($id);
        }
        return null;
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
            'allocations.min' => 'Minimal harus ada 1 alokasi pembayaran',
            'allocations.*.allocated_amount.min' => 'Jumlah alokasi harus lebih dari 0',
            'sisa_pembayaran.min' => 'Sisa pembayaran tidak boleh minus'
        ];
    }
}
