<?php
// Create migration: php artisan make:migration add_sisa_pembayaran_to_pembayarans_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->decimal('sisa_pembayaran', 15, 2)->default(0)->after('nominal_pembayaran');
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn('sisa_pembayaran');
        });
    }
};
