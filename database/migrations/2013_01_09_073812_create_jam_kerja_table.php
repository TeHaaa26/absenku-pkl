    <?php
    // database/migrations/2024_01_01_000003_create_jam_kerja_table.php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        public function up(): void
        {
            Schema::create('jam_kerja', function (Blueprint $table) {
                $table->id();
                $table->string('nama_shift', 150)->default('tidak ada nama');
                $table->time('jam_masuk');
                $table->time('jam_pulang');
                $table->time('batas_absen_masuk');
                $table->time('batas_absen_pulang');
                $table->timestamps();
            });
        }

        public function down(): void
        {
            Schema::dropIfExists('jam_kerja');
        }
    };