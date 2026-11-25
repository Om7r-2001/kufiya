<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'تصميم وجرافيك',
                'slug' => 'design-graphic',
                'description' => 'خدمات التصميم والهوية البصرية',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'برمجة وتطوير',
                'slug' => 'programming-dev',
                'description' => 'تطوير المواقع والتطبيقات',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تسويق إلكتروني',
                'slug' => 'digital-marketing',
                'description' => 'إدارة الحملات والظهور الرقمي',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
