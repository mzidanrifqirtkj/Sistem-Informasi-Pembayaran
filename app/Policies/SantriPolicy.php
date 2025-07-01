<?php
namespace App\Policies;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SantriPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any santri.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('santri.view');
    }

    /**
     * Determine whether the user can view the santri.
     */
    public function view(User $user, Santri $santri)
    {
        // Check basic permission first
        if (!$user->hasPermissionTo('santri.view')) {
            return false;
        }

        // Admin can view all santri
        if ($user->hasRole('admin')) {
            return true;
        }

        // Santri can only view own data
        if ($user->hasRole('santri') && !$user->santri->is_ustadz) {
            return $user->santri->id_santri === $santri->id_santri;
        }

        // Ustadz can view:
// 1. Own data if they are also a santri
// 2. Academic data of santri in classes they teach (but NOT personal profile)
        if ($user->hasRole('ustadz') && $user->santri && $user->santri->is_ustadz) {
            // Own data
            if ($user->santri->id_santri === $santri->id_santri) {
                return true;
            }

            // For other santri: only allow access for academic purposes
// This will be handled by specific academic policies
// Ustadz should NOT access santri profiles directly
            return false;
        }

        return false;
    }

    /**
     * Determine whether the user can create santri.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('santri.create');
    }

    /**
     * Determine whether the user can update the santri.
     */
    public function update(User $user, Santri $santri)
    {
        if (!$user->hasPermissionTo('santri.edit')) {
            return false;
        }

        // Only admin can edit santri data
// Santri and ustadz can only edit their own profile via profile controller
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the santri.
     */
    public function delete(User $user, Santri $santri)
    {
        if (!$user->hasPermissionTo('santri.delete')) {
            return false;
        }

        // Only admin can delete
        return $user->hasRole('admin');
    }

    /**
     * Helper: Check if ustadz teaches santri in any class (current academic year)
     */
    public function ustadzTeachesSantri(User $ustadzUser, Santri $santri)
    {
        if (!$ustadzUser->santri || !$ustadzUser->santri->is_ustadz) {
            return false;
        }

        $ustadz = $ustadzUser->santri;

        // Get classes taught by ustadz in active academic year
        $kelasUstadz = $this->getKelasUstadzAktif($ustadz);

        // Get santri's current class
        $kelasSantri = $this->getKelasSantriAktif($santri);

        return $kelasUstadz->contains($kelasSantri?->id_kelas);
    }

    /**
     * Get classes taught by ustadz in active academic year
     */
    private function getKelasUstadzAktif($ustadz)
    {
        return $ustadz->qoriKelas()
            ->whereHas('mapelKelas', function ($q) {
                $q->whereHas('tahunAjar', function ($ta) {
                    $ta->where('status', 'aktif');
                });
            })
            ->with('mapelKelas.kelas')
            ->get()
            ->pluck('mapelKelas.kelas')
            ->filter()
            ->unique('id_kelas');
    }

    /**
     * Get santri's current active class
     */
    private function getKelasSantriAktif($santri)
    {
        // Get latest riwayat kelas
        $riwayatTerbaru = $santri->riwayatKelas()
            ->with('mapelKelas.kelas')
            ->latest()
            ->first();

        return $riwayatTerbaru?->mapelKelas?->kelas;
    }

    /**
     * Get santri in classes taught by ustadz
     */
    public function getSantriDiKelasUstadz($ustadz)
    {
        $kelasIds = $this->getKelasUstadzAktif($ustadz)->pluck('id_kelas');

        if ($kelasIds->isEmpty()) {
            return collect();
        }

        // Get santri from those classes via riwayat_kelas
        return Santri::whereHas('riwayatKelas', function ($q) use ($kelasIds) {
            $q->whereHas('mapelKelas.kelas', function ($k) use ($kelasIds) {
                $k->whereIn('id_kelas', $kelasIds);
            })
                ->latest() // Get latest class history
                ->limit(1); // One per santri
        })->get();
    }
}
