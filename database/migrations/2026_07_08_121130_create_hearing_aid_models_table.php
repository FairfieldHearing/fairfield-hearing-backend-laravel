<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hearing_aid_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained('manufacturers')->onDelete('cascade');
            $table->string('name');
            $table->integer('mrp');
            $table->float('discount_pct')->default(0.0);
            $table->json('key_features')->nullable();
            $table->json('tags')->nullable();
            $table->string('tech_level');
            $table->string('form_factor');
            $table->integer('units')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed hearing aid models dynamically from public/assets/js/catalog.js
        $path = public_path('assets/js/catalog.js');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (preg_match('/const CATALOG\s*=\s*([\s\S]+?);?$/m', $content, $matches)) {
                $catalogJson = trim($matches[1]);
                $catalog = json_decode($catalogJson, true);
                if (is_array($catalog)) {
                    foreach ($catalog as $index => $item) {
                        $mName = $item['brand'];
                        $manufacturerId = DB::table('manufacturers')->where('name', $mName)->value('id');
                        
                        if (!$manufacturerId) {
                            $manufacturerId = DB::table('manufacturers')->insertGetId([
                                'name' => $mName,
                                'logo_path' => 'assets/img/logo.jpeg',
                                'is_active' => true,
                                'show_on_homepage' => true,
                                'sort_order' => 10,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                        
                        $units = (int)($item['units'] ?? 1);
                        $mrp = (int)($item['mrp'] ?? 0);
                        $perEar = $mrp / $units;
                        $discountPct = 55.0;
                        if ($perEar <= 50000) {
                            $discountPct = 45.0;
                        } elseif ($perEar <= 125000) {
                            $discountPct = 50.0;
                        }
                        
                        DB::table('hearing_aid_models')->insert([
                            'manufacturer_id' => $manufacturerId,
                            'name' => $item['name'],
                            'mrp' => $mrp,
                            'discount_pct' => $discountPct,
                            'key_features' => json_encode($item['feats'] ?? []),
                            'tags' => json_encode([$item['tech'] ?? 'Standard', $item['form'] ?? 'RIC']),
                            'tech_level' => $item['tech'] ?? 'Standard',
                            'form_factor' => $item['form'] ?? 'RIC',
                            'units' => $units,
                            'is_active' => true,
                            'sort_order' => $index,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearing_aid_models');
    }
};
