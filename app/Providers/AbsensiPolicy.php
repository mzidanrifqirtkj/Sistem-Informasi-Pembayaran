<?php
namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;
use App\Policies\SantriPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;

    protected $santriPolicy;

    public function __construct()
    {
        $this->santriPolicy = new SantriPolicy();
    }

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('absensi.view');
    }

    public function view(User $user, Absensi $absensi)
    {
        if (!$user->hasPermissionTo('absensi.view')) {
            return false;
        }

        // Admin can view all
        if ($user->hasRole('admin')) {
            return true;
        }

        $santri = $absensi->santri; // Assuming absensi belongs to santri

        // Santri can view own absensi
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            return $user->santri->id_santri === $santri->id_santri;
        }

        // Ustadz can view absensi of santri in classes they teach
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Own data
            if ($user->santri->id_santri === $santri->id_santri) {
                return true;
            }

            // Santri in taught classes (active academic year only)
            return $this->santriPolicy->ustadzTeachesSantri($user, $santri);
        }

        return false;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('absensi.create') &&
            $user->hasRole(['admin', 'ustadz']);
    }

    public function update(User $user, Absensi $absensi)
    {
        if (!$user->hasPermissionTo('absensi.edit')) {
            return false;
        }

        // Admin can edit all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Ustadz can edit absensi of santri in classes they teach
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            $santri = $absensi->santri;
            return $this->santriPolicy->ustadzTeachesSantri($user, $santri);
        }

        return false;
    }

    public function delete(User $user, Absensi $absensi)
    {
        if (!$user->hasPermissionTo('absensi.delete')) {
            return false;
        }

        // Admin can delete all
        if ($user->hasRole('admin')) {
            return true;
        }

        // Ustadz can delete absensi of santri in classes they teach
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            $santri = $absensi->santri;
            return $this->santriPolicy->ustadzTeachesSantri($user, $santri);
        }

        return false;
    }
}
