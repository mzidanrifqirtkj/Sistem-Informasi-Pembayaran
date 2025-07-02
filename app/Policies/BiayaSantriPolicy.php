<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BiayaSantri;
use Illuminate\Auth\Access\HandlesAuthorization;

class BiayaSantriPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('biaya-santri.view');
    }

    public function view(User $user, BiayaSantri $biayaSantri): bool
    {
        if ($user->hasRole('santri')) {
            return $user->santri && $user->santri->id_santri === $biayaSantri->santri_id;
        }

        return $user->can('biaya-santri.view');
    }

    public function create(User $user): bool
    {
        return $user->can('biaya-santri.create');
    }

    public function update(User $user, BiayaSantri $biayaSantri): bool
    {
        if ($user->hasRole('santri')) {
            return false;
        }

        return $user->can('biaya-santri.edit');
    }

    public function delete(User $user, BiayaSantri $biayaSantri): bool
    {
        if ($user->hasRole('santri')) {
            return false;
        }

        return $user->can('biaya-santri.delete');
    }
}
