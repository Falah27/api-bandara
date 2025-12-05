<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddHierarchyToAirportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airports', function (Blueprint $table) {
            // 1. Tambah kolom baru
            $table->string('parent_id', 20)->nullable()->after('id');
            $table->enum('level', ['cabang_utama', 'cabang_pembantu', 'unit'])
                  ->default('cabang_utama')->after('parent_id');
            $table->text('service_level')->nullable()->after('level');
            $table->string('code', 10)->nullable()->after('name');
            $table->boolean('is_active')->default(true)->after('service_level');
            $table->boolean('has_reports')->default(false)->after('is_active');
        });
        
        // 2. Ubah coordinates & safetyReport jadi nullable
        DB::statement('ALTER TABLE airports MODIFY coordinates JSON NULL');
        DB::statement('ALTER TABLE airports MODIFY safetyReport JSON NULL');
        
        // 3. Tambah indexes
        Schema::table('airports', function (Blueprint $table) {
            $table->index('parent_id');
            $table->index('level');
            $table->index('is_active');
        });
        
        // 4. Update existing data (28 cabang utama yang sudah ada)
        DB::table('airports')->update([
            'level' => 'cabang_utama',
            'parent_id' => null,
            'is_active' => true,
            'has_reports' => DB::raw('total_reports > 0'),
            'code' => DB::raw('id')
        ]);
        
        // 5. Tambah foreign key (di akhir setelah data ready)
        Schema::table('airports', function (Blueprint $table) {
            $table->foreign('parent_id')
                  ->references('id')->on('airports')
                  ->onDelete('cascade');
        });
        
        echo "\n✅ Migration complete! Structure ready for 301 locations.\n";
        echo "📝 Next: php artisan db:seed --class=HierarchySeeder\n\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airports', function (Blueprint $table) {
            // Hapus foreign key dulu
            $table->dropForeign(['parent_id']);
            
            // Hapus indexes
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['level']);
            $table->dropIndex(['is_active']);
            
            // Hapus kolom
            $table->dropColumn([
                'parent_id',
                'level',
                'service_level',
                'code',
                'is_active',
                'has_reports'
            ]);
        });
        
        echo "\n✅ Rollback complete!\n\n";
    }
}