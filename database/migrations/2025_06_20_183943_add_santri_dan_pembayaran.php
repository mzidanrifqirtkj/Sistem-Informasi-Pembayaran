<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // --- Update tabel santris ---
        Schema::table('santris', function (Blueprint $table) {

            $table->string('status_reason', 50)
                ->nullable()
                ->after('status')
                ->comment('alumni, keluar, cuti, lainnya');

            $table->date('status_changed_at')
                ->nullable()
                ->after('status_reason');

            $table->text('status_notes')
                ->nullable()
                ->after('status_changed_at');
        });

        // Set semua santri menjadi aktif sebagai nilai awal
        DB::table('santris')->update(['status' => 'aktif']);

        // --- Update tabel pembayarans ---
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->boolean('is_void')
                ->default(false)
                ->after('total_allocations')
                ->index();

            $table->timestamp('voided_at')
                ->nullable()
                ->after('is_void');

            $table->unsignedBigInteger('voided_by')
                ->nullable()
                ->after('voided_at');

            $table->text('void_reason')
                ->nullable()
                ->after('voided_by');

            $table->string('payment_note')
                ->nullable()
                ->after('tanggal_pembayaran')
                ->comment('Catatan pembayaran, misal: cicilan 1 dari 3');

            $table->string('receipt_number', 30)
                ->nullable()
                ->unique()
                ->after('payment_note')
                ->comment('Format: KWT/YYYY/MM/XXXX');

            $table->foreign('voided_by')
                ->references('id_user')
                ->on('users')
                ->onDelete('set null');

            $table->index(['is_void', 'tanggal_pembayaran']);
            $table->index('receipt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback santris
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'status_reason',
                'status_changed_at',
                'status_notes'
            ]);
        });

        // Rollback pembayarans
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['voided_by']);
            $table->dropColumn([
                'is_void',
                'voided_at',
                'voided_by',
                'void_reason',
                'payment_note',
                'receipt_number'
            ]);
        });
    }
};
