<?php

namespace App\Providers;

use App\Models\Santri;
use App\Models\RiwayatKelas;
use App\Policies\SantriPolicy;
use App\Policies\RiwayatKelasPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Santri::class => SantriPolicy::class,
        RiwayatKelas::class => RiwayatKelasPolicy::class,
        BiayaSantri::class => BiayaSantriPolicy::class,
        \App\Models\TagihanBulanan::class => \App\Policies\TagihanBulananPolicy::class,
        \App\Models\TagihanTerjadwal::class => \App\Policies\TagihanTerjadwalPolicy::class,
        \App\Models\Pembayaran::class => \App\Policies\PembayaranPolicy::class,

        // Future policies (uncomment when models are ready)
        // Absensi::class => AbsensiPolicy::class,
        // Penilaian::class => PenilaianPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Additional gates if needed
        Gate::define('viewAny-santri-in-taught-classes', function ($user) {
            return $user->hasRole(['admin', 'ustadz']);
        });
    }
}
