<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->text('json_schema')->nullable();
        });

        // Seed default JSON-LD schemas
        $defaults = [
            'home' => [
                '@context' => 'https://schema.org',
                '@type' => 'MedicalBusiness',
                'name' => 'Fairfield Hearing Clinics',
                'image' => 'https://fairfieldhearing.in/assets/img/logo.jpeg',
                'telephone' => '+91-9811418578',
                'email' => 'info@fairfieldhearing.in',
                'priceRange' => '$$',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'New Friends Colony',
                    'addressLocality' => 'New Delhi',
                    'addressRegion' => 'Delhi',
                    'postalCode' => '110025',
                    'addressCountry' => 'IN'
                ]
            ],
            'about' => [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'About Us | Fairfield Hearing Clinics',
                'description' => 'Learn about Fairfield Hearing Clinics\' mission, values, ENT specialists, and RCI-certified audiologists providing professional, transparent hearing care in Delhi.'
            ],
            'book_test' => [
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Book Free Hearing Test & Trial - Fairfield Hearing Clinics',
                'description' => 'Schedule your free professional hearing test or hearing aid trial with Fairfield Hearing Clinics in Delhi. Easy online booking.'
            ],
            'exchange' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Hearing Aid Exchange Program - Fairfield Hearing Clinics',
                'description' => 'Upgrade your old hearing aid with our dynamic Exchange Program. Calculate your old device value and get the best discount on new devices.'
            ],
            'tech_bluetooth' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Bluetooth Hearing Aids - Fairfield Hearing Clinics',
                'description' => 'Explore bluetooth enabled hearing aids with direct streaming for iOS and Android.'
            ],
            'tech_bte' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Behind-The-Ear (BTE) Hearing Aids - Fairfield Hearing Clinics',
                'description' => 'Discover robust Behind-The-Ear (BTE) hearing aids suitable for all types of hearing loss.'
            ],
            'tech_invisible' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Invisible Hearing Aids (IIC/CIC) - Fairfield Hearing Clinics',
                'description' => 'Get completely invisible-in-canal (IIC) and completely-in-canal (CIC) hearing aids.'
            ],
            'tech_rechargeable' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Rechargeable Hearing Aids - Fairfield Hearing Clinics',
                'description' => 'No more tiny batteries. Check out rechargeable hearing aids with long-lasting battery life.'
            ],
            'tech_ric' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Receiver-In-Canal (RIC) Hearing Aids - Fairfield Hearing Clinics',
                'description' => 'Explore Receiver-In-Canal (RIC) hearing aids. The most popular, comfortable design.'
            ],
            'tech_tinnitus' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => 'Tinnitus Relief & Management Hearing Aids - Fairfield Hearing',
                'description' => 'Find relief from ringing in the ears. Explore hearing aids featuring built-in tinnitus sound generators.'
            ],
            'blogs_index' => [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => 'Hearing Health Blog - Fairfield Hearing Clinics',
                'description' => 'Read articles, tips, and clinical guides on hearing health, modern hearing aid technology, and sound care advice from our audiologists.'
            ],
        ];

        foreach ($defaults as $key => $schema) {
            DB::table('page_settings')
                ->where('page_key', $key)
                ->update(['json_schema' => json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_settings', function (Blueprint $table) {
            $table->dropColumn('json_schema');
        });
    }
};
