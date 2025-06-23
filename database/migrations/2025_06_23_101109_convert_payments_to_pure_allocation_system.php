<?php
// Create migration: php artisan make:migration convert_payments_to_pure_allocation_system

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::beginTransaction();

        try {
            // Step 1: Check current data
            $directBulananCount = DB::table('pembayarans')
                ->whereNotNull('tagihan_bulanan_id')
                ->whereNull('payment_type')
                ->count();

            $directTerjadwalCount = DB::table('pembayarans')
                ->whereNotNull('tagihan_terjadwal_id')
                ->whereNull('payment_type')
                ->count();

            echo "Converting {$directBulananCount} bulanan payments and {$directTerjadwalCount} terjadwal payments to allocation system...\n";

            // Step 2: Convert direct bulanan payments to allocations
            $bulananPayments = DB::table('pembayarans')
                ->whereNotNull('tagihan_bulanan_id')
                ->whereNull('payment_type')
                ->get();

            foreach ($bulananPayments as $payment) {
                // Create allocation record
                DB::table('payment_allocations')->insert([
                    'pembayaran_id' => $payment->id_pembayaran,
                    'tagihan_bulanan_id' => $payment->tagihan_bulanan_id,
                    'allocated_amount' => $payment->nominal_pembayaran,
                    'allocation_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update pembayaran to pure allocation
                DB::table('pembayarans')
                    ->where('id_pembayaran', $payment->id_pembayaran)
                    ->update([
                        'tagihan_bulanan_id' => null,
                        'payment_type' => 'allocated',
                        'total_allocations' => 1,
                        'updated_at' => now()
                    ]);
            }

            // Step 3: Convert direct terjadwal payments to allocations
            $terjadwalPayments = DB::table('pembayarans')
                ->whereNotNull('tagihan_terjadwal_id')
                ->whereNull('payment_type')
                ->get();

            foreach ($terjadwalPayments as $payment) {
                // Create allocation record
                DB::table('payment_allocations')->insert([
                    'pembayaran_id' => $payment->id_pembayaran,
                    'tagihan_terjadwal_id' => $payment->tagihan_terjadwal_id,
                    'allocated_amount' => $payment->nominal_pembayaran,
                    'allocation_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update pembayaran to pure allocation
                DB::table('pembayarans')
                    ->where('id_pembayaran', $payment->id_pembayaran)
                    ->update([
                        'tagihan_terjadwal_id' => null,
                        'payment_type' => 'allocated',
                        'total_allocations' => 1,
                        'updated_at' => now()
                    ]);
            }

            // Step 4: Verify conversion
            $remainingDirect = DB::table('pembayarans')
                ->where(function($q) {
                    $q->whereNotNull('tagihan_bulanan_id')
                      ->orWhereNotNull('tagihan_terjadwal_id');
                })
                ->whereNull('payment_type')
                ->count();

            if ($remainingDirect > 0) {
                throw new \Exception("Migration incomplete: {$remainingDirect} payments still have direct links");
            }

            echo "Migration completed successfully!\n";
            echo "Converted " . ($directBulananCount + $directTerjadwalCount) . " payments to pure allocation system.\n";

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function down()
    {
        DB::beginTransaction();

        try {
            // Revert: Convert allocations back to direct links
            $allocatedPayments = DB::table('pembayarans')
                ->where('payment_type', 'allocated')
                ->where('total_allocations', 1)
                ->get();

            foreach ($allocatedPayments as $payment) {
                // Get the single allocation
                $allocation = DB::table('payment_allocations')
                    ->where('pembayaran_id', $payment->id_pembayaran)
                    ->first();

                if ($allocation) {
                    // Restore direct link
                    $updateData = [
                        'payment_type' => null,
                        'total_allocations' => null,
                        'updated_at' => now()
                    ];

                    if ($allocation->tagihan_bulanan_id) {
                        $updateData['tagihan_bulanan_id'] = $allocation->tagihan_bulanan_id;
                    }

                    if ($allocation->tagihan_terjadwal_id) {
                        $updateData['tagihan_terjadwal_id'] = $allocation->tagihan_terjadwal_id;
                    }

                    DB::table('pembayarans')
                        ->where('id_pembayaran', $payment->id_pembayaran)
                        ->update($updateData);

                    // Delete allocation record
                    DB::table('payment_allocations')
                        ->where('pembayaran_id', $payment->id_pembayaran)
                        ->delete();
                }
            }

            DB::commit();
            echo "Rollback completed successfully!\n";

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
};
