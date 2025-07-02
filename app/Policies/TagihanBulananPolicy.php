<?php
namespace App\Policies;

use App\Models\User;
use App\Models\TagihanBulanan;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagihanBulananPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('tagihan-bulanan.view');
    }

    public function view(User $user, TagihanBulanan $tagihan)
    {
        if (!$user->hasPermissionTo('tagihan-bulanan.view'))
            return false;

        if ($user->hasRole('admin'))
            return true;

        return $user->hasRole('santri') && $user->santri->id_santri === $tagihan->santri_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('tagihan-bulanan.create');
    }

    public function update(User $user, TagihanBulanan $tagihan)
    {
        return $user->hasPermissionTo('tagihan-bulanan.edit') && $user->hasRole('admin');
    }

    public function delete(User $user, TagihanBulanan $tagihan)
    {
        return $user->hasPermissionTo('tagihan-bulanan.delete') && $user->hasRole('admin');
    }
}
