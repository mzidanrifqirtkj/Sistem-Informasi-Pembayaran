<?php
namespace App\Policies;

use App\Models\User;
use App\Models\TagihanTerjadwal;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagihanTerjadwalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('tagihan-terjadwal.view');
    }

    public function view(User $user, TagihanTerjadwal $tagihan)
    {
        if (!$user->hasPermissionTo('tagihan-terjadwal.view'))
            return false;

        if ($user->hasRole('admin'))
            return true;

        return $user->hasRole('santri') && $user->santri->id_santri === $tagihan->santri_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('tagihan-terjadwal.create');
    }

    public function update(User $user, TagihanTerjadwal $tagihan)
    {
        return $user->hasPermissionTo('tagihan-terjadwal.edit') && $user->hasRole('admin');
    }

    public function delete(User $user, TagihanTerjadwal $tagihan)
    {
        return $user->hasPermissionTo('tagihan-terjadwal.delete') && $user->hasRole('admin');
    }
}
