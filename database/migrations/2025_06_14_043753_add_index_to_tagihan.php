<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to tagihan_terjadwals table for performance optimization
        Schema::table('tagihan_terjadwals', function (Blueprint $table) {
            // Index for filtering by santri and tahun (frequently used in queries)
            $table->index(['santri_id', 'tahun', 'deleted_at'], 'idx_tagihan_terjadwal_santri_tahun');

            // Index for duplicate checking (biaya_santri_id + tahun + tahun_ajar_id)
            $table->index(['biaya_santri_id', 'tahun', 'tahun_ajar_id'], 'idx_tagihan_terjadwal_duplicate_check');

            // Index for status filtering
            $table->index(['status', 'deleted_at'], 'idx_tagihan_terjadwal_status');

            // Index for tahun filtering
            $table->index(['tahun', 'deleted_at'], 'idx_tagihan_terjadwal_tahun');

            // Index for daftar_biaya_id (for joins with daftar_biayas)
            $table->index(['daftar_biaya_id', 'deleted_at'], 'idx_tagihan_terjadwal_daftar_biaya');
        });

        // Add indexes to pembayarans table for better join performance
        Schema::table('pembayarans', function (Blueprint $table) {
            // Index for tagihan_terjadwal_id (frequently joined)
            if (!Schema::hasIndex('pembayarans', 'idx_pembayaran_tagihan_terjadwal')) {
                $table->index('tagihan_terjadwal_id', 'idx_pembayaran_tagihan_terjadwal');
            }

            // Index for tagihan_bulanan_id (frequently joined)
            if (!Schema::hasIndex('pembayarans', 'idx_pembayaran_tagihan_bulanan')) {
                $table->index('tagihan_bulanan_id', 'idx_pembayaran_tagihan_bulanan');
            }

            // Index for tanggal_pembayaran (for date range queries)
            if (!Schema::hasIndex('pembayarans', 'idx_pembayaran_tanggal')) {
                $table->index('tanggal_pembayaran', 'idx_pembayaran_tanggal');
            }
        });

        // Add indexes to biaya_santris table
        Schema::table('biaya_santris', function (Blueprint $table) {
            // Index for santri_id (frequently used in getBiayaSantriBySantriId)
            if (!Schema::hasIndex('biaya_santris', 'idx_biaya_santri_santri_id')) {
                $table->index('santri_id', 'idx_biaya_santri_santri_id');
            }

            // Index for daftar_biaya_id (for joins)
            if (!Schema::hasIndex('biaya_santris', 'idx_biaya_santri_daftar_biaya')) {
                $table->index('daftar_biaya_id', 'idx_biaya_santri_daftar_biaya');
            }
        });

        // Add indexes to daftar_biayas table
        Schema::table('daftar_biayas', function (Blueprint $table) {
            // Index for kategori_biaya_id (for joins with kategori_biayas)
            if (!Schema::hasIndex('daftar_biayas', 'idx_daftar_biaya_kategori')) {
                $table->index('kategori_biaya_id', 'idx_daftar_biaya_kategori');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from tagihan_terjadwals
        Schema::table('tagihan_terjadwals', function (Blueprint $table) {
            $table->dropIndex('idx_tagihan_terjadwal_santri_tahun');
            $table->dropIndex('idx_tagihan_terjadwal_duplicate_check');
            $table->dropIndex('idx_tagihan_terjadwal_status');
            $table->dropIndex('idx_tagihan_terjadwal_tahun');
            $table->dropIndex('idx_tagihan_terjadwal_daftar_biaya');
        });

        // Drop indexes from pembayarans
        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasIndex('pembayarans', 'idx_pembayaran_tagihan_terjadwal')) {
                $table->dropIndex('idx_pembayaran_tagihan_terjadwal');
            }
            if (Schema::hasIndex('pembayarans', 'idx_pembayaran_tagihan_bulanan')) {
                $table->dropIndex('idx_pembayaran_tagihan_bulanan');
            }
            if (Schema::hasIndex('pembayarans', 'idx_pembayaran_tanggal')) {
                $table->dropIndex('idx_pembayaran_tanggal');
            }
        });

        // Drop indexes from biaya_santris
        Schema::table('biaya_santris', function (Blueprint $table) {
            if (Schema::hasIndex('biaya_santris', 'idx_biaya_santri_santri_id')) {
                $table->dropIndex('idx_biaya_santri_santri_id');
            }
            if (Schema::hasIndex('biaya_santris', 'idx_biaya_santri_daftar_biaya')) {
                $table->dropIndex('idx_biaya_santri_daftar_biaya');
            }
        });

        // Drop indexes from daftar_biayas
        Schema::table('daftar_biayas', function (Blueprint $table) {
            if (Schema::hasIndex('daftar_biayas', 'idx_daftar_biaya_kategori')) {
                $table->dropIndex('idx_daftar_biaya_kategori');
            }
        });
    }
};
