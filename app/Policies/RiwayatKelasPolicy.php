<?php
namespace App\Policies;

use App\Models\RiwayatKelas;
use App\Models\User;
use App\Policies\SantriPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiwayatKelasPolicy
{
    use HandlesAuthorization;

    protected $santriPolicy;

    public function __construct()
    {
        $this->santriPolicy = new SantriPolicy();
    }

    /**
     * Determine whether the user can view any riwayat kelas.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('riwayat-kelas.view');
    }

    /**
     * Determine whether the user can view the riwayat kelas.
     */
    public function view(User $user, RiwayatKelas $riwayatKelas)
    {
        if (!$user->hasPermissionTo('riwayat-kelas.view')) {
            return false;
        }

        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        $santri = $riwayatKelas->santri;
        if (!$santri) {
            return false;
        }

        // Santri can view own riwayat kelas
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            return $user->santri->id_santri === $santri->id_santri;
        }

        // Ustadz can view riwayat kelas of santri in classes they teach
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Own data
            if ($user->santri->id_santri === $santri->id_santri) {
                return true;
            }

            // Santri in taught classes
            return $this->santriPolicy->ustadzTeachesSantri($user, $santri);
        }

        return false;
    }

    /**
     * Determine whether the user can create riwayat kelas.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('riwayat-kelas.create') && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the riwayat kelas.
     */
    public function update(User $user, RiwayatKelas $riwayatKelas)
    {
        return $user->hasPermissionTo('riwayat-kelas.edit') && $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the riwayat kelas.
     */
    public function delete(User $user, RiwayatKelas $riwayatKelas)
    {
        return $user->hasPermissionTo('riwayat-kelas.delete') && $user->hasRole('admin');
    }
}
