<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ========================================
 * CLEANUP DUPLICATES
 * ========================================
 * Hapus 82 data duplikat, sisakan 28 cabang utama
 * ========================================
 */
class CleanupDuplicates extends Seeder
{
    /**
     * 28 Cabang Utama yang VALID (yang punya koordinat)
     */
    private $validCabangUtama = [
        'JATSC', 'MATSC', 'KNO', 'PLM', 'YIA', 'SUB', 'DPS', 'BPN',
        'DJJ', 'PNK', 'BTJ', 'PKU', 'TNJ', 'HLP', 'BDO', 'SRG',
        'BDJ', 'PKY', 'TRK', 'MDC', 'KDI', 'LOP', 'KOE', 'AMQ',
        'WMX', 'NBX', 'SOQ', 'MKQ'
    ];

    public function run()
    {
        $this->command->info("🧹 Starting Cleanup...\n");
        
        DB::beginTransaction();
        
        try {
            // 1. Cek total sebelum cleanup
            $totalBefore = Airport::count();
            $this->command->info("📊 Before cleanup: $totalBefore airports");
            
            // 2. Ambil semua ID yang bukan 28 cabang utama
            $duplicates = Airport::whereNotIn('id', $this->validCabangUtama)
                ->where('level', 'cabang_utama')
                ->get();
            
            $this->command->info("🔍 Found " . $duplicates->count() . " duplicate cabang_utama");
            
            if ($duplicates->count() > 0) {
                $this->command->info("\n⚠️  Will delete:");
                foreach ($duplicates as $dup) {
                    $this->command->warn("   - {$dup->id}: {$dup->name}");
                }
                
                if ($this->command->confirm('Delete these duplicates?', true)) {
                    // Hapus duplikat
                    Airport::whereNotIn('id', $this->validCabangUtama)
                        ->where('level', 'cabang_utama')
                        ->delete();
                    
                    $this->command->info("\n✅ Deleted " . $duplicates->count() . " duplicates");
                }
            }
            
            // 3. Pastikan 28 cabang utama set dengan benar
            Airport::whereIn('id', $this->validCabangUtama)
                ->update([
                    'level' => 'cabang_utama',
                    'parent_id' => null,
                    'is_active' => true
                ]);
            
            $this->command->info("✅ Updated 28 valid cabang_utama");
            
            // 4. Cek hasil
            $totalAfter = Airport::count();
            $breakdown = Airport::select('level', DB::raw('count(*) as total'))
                ->groupBy('level')
                ->pluck('total', 'level');
            
            $this->command->info("\n" . str_repeat("=", 50));
            $this->command->info("📊 AFTER CLEANUP");
            $this->command->info(str_repeat("=", 50));
            $this->command->info("Total airports: $totalAfter");
            $this->command->info("- Cabang Utama: " . ($breakdown['cabang_utama'] ?? 0));
            $this->command->info("- Cabang Pembantu: " . ($breakdown['cabang_pembantu'] ?? 0));
            $this->command->info("- Unit: " . ($breakdown['unit'] ?? 0));
            
            // Expected: 28 cabang_utama
            if (($breakdown['cabang_utama'] ?? 0) == 28) {
                $this->command->info("\n🎉 PERFECT! 28 Cabang Utama");
            } else {
                $this->command->warn("\n⚠️  Warning: Expected 28 cabang_utama, got " . ($breakdown['cabang_utama'] ?? 0));
            }
            
            DB::commit();
            
            $this->command->info("\n✅ Cleanup complete!");
            $this->command->info("📝 Next: php artisan db:seed --class=HierarchySeeder\n");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
            throw $e;
        }
    }
}