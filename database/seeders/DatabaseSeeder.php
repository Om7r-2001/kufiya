<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'استشارات قانونية',
                'slug' => 'legal-consultations',
                'description' => 'استشارات وصياغة عقود ومتابعة قانونية',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'برمجة وتطوير',
                'slug' => 'programming-development',
                'description' => 'تطوير مواقع وأنظمة وتطبيقات وقواعد بيانات',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تصميم واجهات',
                'slug' => 'ui-ux-design',
                'description' => 'تصميم UI/UX وتجربة مستخدم وواجهات احترافية',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تعليم وتدريب',
                'slug' => 'education-training',
                'description' => 'دروس، كورسات، تدريب مهني وتقني',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'خدمات رقمية',
                'slug' => 'digital-services',
                'description' => 'تصميم، تسويق، محتوى، خدمات عبر الإنترنت',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'خدمات مالية',
                'slug' => 'financial-services',
                'description' => 'محاسبة، مسك دفاتر، استشارات مالية',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'خدمات مهنية',
                'slug' => 'professional-services',
                'description' => 'بناء، صيانة، كهرباء، سباكة، أعمال مهنية',
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
