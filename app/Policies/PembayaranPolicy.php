<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Pembayaran;
use Illuminate\Auth\Access\HandlesAuthorization;

class PembayaranPolicy
{
    use HandlesAuthorization;

    /**
     * Untuk halaman index daftar santri (pembayaran.index)
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('pembayaran.list');
    }

    /**
     * Untuk melihat struk pembayaran (pembayaran.receipt dan printReceipt)
     */
    public function view(User $user, Pembayaran $pembayaran)
    {
        if (!$user->hasPermissionTo('pembayaran.view')) {
            return false;
        }

        if ($user->hasRole('admin'))
            return true;

        // Ambil ID santri dari tagihan yang terhubung ke pembayaran
        $santriId = $pembayaran->tagihanBulanan?->santri_id
            ?? $pembayaran->tagihanTerjadwal?->santri_id
            ?? $pembayaran->paymentAllocations->first()?->tagihanBulanan?->santri_id
            ?? $pembayaran->paymentAllocations->first()?->tagihanTerjadwal?->santri_id;

        return $user->hasRole('santri') && $user->santri->id_santri === $santriId;
    }

    /**
     * Untuk show(), preview(), dan store()
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('pembayaran.create');
    }

    /**
     * Untuk riwayat pembayaran (history)
     */
    public function history(User $user)
    {
        return $user->hasPermissionTo('pembayaran.history');
    }

    /**
     * Untuk pembatalan pembayaran (tidak ada di controller ini, tapi sebaiknya disiapkan)
     */
    public function void(User $user, Pembayaran $pembayaran)
    {
        return $user->hasPermissionTo('pembayaran.void') && $user->hasRole('admin');
    }
}
