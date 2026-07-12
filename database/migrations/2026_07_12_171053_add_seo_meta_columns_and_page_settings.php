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
        // 1. Add columns to blog_posts
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
        });

        // 2. Add columns to blog_categories
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
        });

        // 3. Add columns to policy_pages
        Schema::table('policy_pages', function (Blueprint $table) {
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
        });

        // 4. Add columns to team_members
        Schema::table('team_members', function (Blueprint $table) {
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
        });

        // 5. Create page_settings table
        Schema::create('page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('page_name');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();
        });

        // 6. Seed default page settings
        $now = now();
        $defaults = [
            [
                'page_key' => 'home',
                'page_name' => 'Home Page',
                'meta_title' => 'Fairfield Hearing Clinics | Free Hearing Test & Hearing Aids in Delhi',
                'meta_description' => 'Fairfield Hearing Clinics — RCI-certified audiologists offering free hearing tests, hearing aid fitting and trials from Signia, Phonak, Widex & more. Book your free hearing test in Delhi today.',
                'meta_keywords' => 'hearing clinic, hearing test, hearing aids delhi, free hearing trial, audiologist delhi',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'about',
                'page_name' => 'About Us Page',
                'meta_title' => 'About Us - Fairfield Hearing Clinics',
                'meta_description' => 'Learn about Fairfield Hearing Clinic. Our RCI-certified audiologists, mission, values, and our dedication to providing personalized hearing care in Delhi.',
                'meta_keywords' => 'about fairfield hearing, audiologists, hearing care professionals delhi',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'book_test',
                'page_name' => 'Book A Test Page',
                'meta_title' => 'Book Free Hearing Test & Trial - Fairfield Hearing Clinics',
                'meta_description' => 'Schedule your free professional hearing test or hearing aid trial with Fairfield Hearing Clinics in Delhi. Easy online booking.',
                'meta_keywords' => 'book hearing test, hearing aid trial delhi, free hearing checkup',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'exchange',
                'page_name' => 'Exchange Offer Page',
                'meta_title' => 'Hearing Aid Exchange Program - Fairfield Hearing Clinics',
                'meta_description' => 'Upgrade your old hearing aid with our dynamic Exchange Program. Calculate your old device value and get the best discount on new devices.',
                'meta_keywords' => 'hearing aid exchange, upgrade hearing aid, old hearing aid value, hearing aid discount',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'tech_bluetooth',
                'page_name' => 'Technology - Bluetooth Page',
                'meta_title' => 'Bluetooth Hearing Aids - Fairfield Hearing Clinics',
                'meta_description' => 'Explore bluetooth enabled hearing aids with direct streaming for iOS and Android. Connect seamlessly to your phone, TV, and other audio devices.',
                'meta_keywords' => 'bluetooth hearing aids, streaming hearing aids, smart hearing aids delhi',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'tech_bte',
                'page_name' => 'Technology - BTE Page',
                'meta_title' => 'Behind-The-Ear (BTE) Hearing Aids - Fairfield Hearing Clinics',
                'meta_description' => 'Discover robust Behind-The-Ear (BTE) hearing aids suitable for all types of hearing loss, offering maximum amplification and durability.',
                'meta_keywords' => 'bte hearing aids, behind the ear hearing aid, powerful hearing aids',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'tech_invisible',
                'page_name' => 'Technology - Invisible Page',
                'meta_title' => 'Invisible Hearing Aids (IIC/CIC) - Fairfield Hearing Clinics',
                'meta_description' => 'Get completely invisible-in-canal (IIC) and completely-in-canal (CIC) hearing aids. Ultra-discreet solutions custom-made for your ear.',
                'meta_keywords' => 'invisible hearing aids, iic hearing aid, cic hearing aids delhi, hidden hearing aids',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'tech_rechargeable',
                'page_name' => 'Technology - Rechargeable Page',
                'meta_title' => 'Rechargeable Hearing Aids - Fairfield Hearing Clinics',
                'meta_description' => 'No more tiny batteries. Check out rechargeable hearing aids with long-lasting battery life, portable charging cases, and quick-charging features.',
                'meta_keywords' => 'rechargeable hearing aids, lithium ion hearing aids, battery free hearing aids',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'tech_ric',
                'page_name' => 'Technology - RIC Page',
                'meta_title' => 'Receiver-In-Canal (RIC) Hearing Aids - Fairfield Hearing Clinics',
                'meta_description' => 'Explore Receiver-In-Canal (RIC) hearing aids. The most popular, comfortable design offering natural sound quality and open fit comfort.',
                'meta_keywords' => 'ric hearing aids, receiver in canal, natural sound hearing aids',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'tech_tinnitus',
                'page_name' => 'Technology - Tinnitus Page',
                'meta_title' => 'Tinnitus Relief & Management Hearing Aids - Fairfield Hearing',
                'meta_description' => 'Find relief from ringing in the ears. Explore hearing aids featuring built-in tinnitus sound generators and therapy systems to soothe your symptoms.',
                'meta_keywords' => 'tinnitus hearing aids, ringing ears relief, tinnitus therapy delhi',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'page_key' => 'blogs_index',
                'page_name' => 'Blogs Main List Page',
                'meta_title' => 'Hearing Health Blog - Fairfield Hearing Clinics',
                'meta_description' => 'Read articles, tips, and clinical guides on hearing health, modern hearing aid technology, and sound care advice from our audiologists.',
                'meta_keywords' => 'hearing health blog, hearing aid guides, audiologist articles, ear health tips',
                'canonical_url' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('page_settings')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_settings');

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'canonical_url']);
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'canonical_url']);
        });

        Schema::table('policy_pages', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'canonical_url']);
        });

        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'canonical_url']);
        });
    }
};
