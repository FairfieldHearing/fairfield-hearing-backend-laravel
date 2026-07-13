<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('locations')
            ->where('name', 'Fairfield Hearing Clinic — Delhi Hari Nagar')
            ->update([
                'google_maps_link' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.0952479892107!2d77.10961089999999!3d28.626907299999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d030e158dc837%3A0x88a0d535b5afce8f!2sFairfield%20Hearing%20Clinics%20%7C%20Hearing%20aid%20clinic%20%7C%20Hearing%20aid%20dealer%20%7C%20Audiologist%20%7C%20Delhi%20%7C%20India!5e0!3m2!1sen!2sin!4v1783664750880!5m2!1sen!2sin'
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('locations')
            ->where('name', 'Fairfield Hearing Clinic — Delhi Hari Nagar')
            ->update([
                'google_maps_link' => 'https://maps.google.com/maps?q=SHOP%20NO%202%2C%20WZ%2C406-L-1%2C%20PLOT%20NUMBER%2054%2C%20JANAK%20PARK%2C%20HARI%20NAGAR%2C%20DELHI%2C%20110064&t=&z=15&ie=UTF8&iwloc=&output=embed'
            ]);
    }
};
