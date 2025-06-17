<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Tambah kolom ke tagihan_bulanans
        Schema::table('tagihan_bulanans', function (Blueprint $table) {
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('bulan_urutan')->after('bulan')->nullable();
            $table->date('due_date')->after('tahun')->nullable();

            $table->index(['santri_id', 'tahun', 'bulan'], 'idx_tagihan_bulanan_lookup');
            $table->index(['status', 'tahun'], 'idx_tagihan_bulanan_status');
            $table->index(['deleted_at'], 'idx_tagihan_bulanan_deleted');
            $table->index(['due_date'], 'idx_tagihan_bulanan_due');
        });

        // 2. Buat tabel payment_allocations
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_id');
            $table->unsignedBigInteger('tagihan_bulanan_id')->nullable();
            $table->unsignedBigInteger('tagihan_terjadwal_id')->nullable();
            $table->decimal('allocated_amount', 12, 0);
            $table->tinyInteger('allocation_order');
            $table->timestamps();

            $table->foreign('pembayaran_id')->references('id_pembayaran')->on('pembayarans')->onDelete('cascade');
            $table->foreign('tagihan_bulanan_id')->references('id_tagihan_bulanan')->on('tagihan_bulanans')->onDelete('set null');
            $table->foreign('tagihan_terjadwal_id')->references('id_tagihan_terjadwal')->on('tagihan_terjadwals')->onDelete('set null');

            $table->index(['pembayaran_id', 'allocation_order'], 'idx_payment_allocation_order');
        });

        // 3. Update pembayarans
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->enum('payment_type', ['single', 'allocated'])->default('single')->after('created_by_id');
            $table->integer('total_allocations')->default(0)->after('payment_type');

            $table->index(['tagihan_bulanan_id', 'created_at'], 'idx_pembayaran_bulanan');
            $table->index(['created_at'], 'idx_pembayaran_date');
        });

        // 4. Buat tabel audit_logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 50);
            $table->unsignedBigInteger('record_id');
            $table->string('action', 20); // created, updated, deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['table_name', 'record_id'], 'idx_audit_table_record');
            $table->index(['user_id'], 'idx_audit_user');
            $table->index(['created_at'], 'idx_audit_date');
        });

        // 5. Buat tabel generate_logs
        Schema::create('generate_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // bulk_tagihan_bulanan, etc
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->json('parameters');
            $table->integer('total_processed')->default(0);
            $table->integer('total_success')->default(0);
            $table->integer('total_failed')->default(0);
            $table->json('errors')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at'], 'idx_generate_log_type');
            $table->index(['user_id'], 'idx_generate_log_user');
        });

        // 6. Update data bulan_urutan
        DB::statement("
            UPDATE tagihan_bulanans
            SET bulan_urutan = CASE bulan
                WHEN 'Jan' THEN 1
                WHEN 'Feb' THEN 2
                WHEN 'Mar' THEN 3
                WHEN 'Apr' THEN 4
                WHEN 'May' THEN 5
                WHEN 'Jun' THEN 6
                WHEN 'Jul' THEN 7
                WHEN 'Aug' THEN 8
                WHEN 'Sep' THEN 9
                WHEN 'Oct' THEN 10
                WHEN 'Nov' THEN 11
                WHEN 'Dec' THEN 12
            END,
            created_at = NOW(),
            updated_at = NOW()
            WHERE bulan_urutan IS NULL
        ");

        // 7. Update due_date
        DB::statement("
            UPDATE tagihan_bulanans
            SET due_date = DATE_ADD(
                STR_TO_DATE(CONCAT('01-', bulan_urutan, '-', tahun), '%d-%m-%Y'),
                INTERVAL 1 MONTH
            )
            WHERE due_date IS NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('generate_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('payment_allocations');

        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'total_allocations']);
            $table->dropIndex('idx_pembayaran_bulanan');
            $table->dropIndex('idx_pembayaran_date');
        });

        Schema::table('tagihan_bulanans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropTimestamps();
            $table->dropColumn(['bulan_urutan', 'due_date']);
            $table->dropIndex('idx_tagihan_bulanan_lookup');
            $table->dropIndex('idx_tagihan_bulanan_status');
            $table->dropIndex('idx_tagihan_bulanan_deleted');
            $table->dropIndex('idx_tagihan_bulanan_due');
        });
    }
};
