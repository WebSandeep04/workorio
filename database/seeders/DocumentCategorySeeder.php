<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentCategory;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Contracts',
                'slug' => 'contracts',
                'description' => 'Contract documents and agreements',
                'icon' => 'bi-file-text-fill',
                'color' => 'primary',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'description' => 'Business reports and analytics',
                'icon' => 'bi-graph-up',
                'color' => 'success',
                'sort_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Invoices',
                'slug' => 'invoices',
                'description' => 'Invoice and billing documents',
                'icon' => 'bi-receipt',
                'color' => 'warning',
                'sort_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Proposals',
                'slug' => 'proposals',
                'description' => 'Project proposals and plans',
                'icon' => 'bi-file-earmark-text',
                'color' => 'info',
                'sort_order' => 4,
                'is_active' => true
            ],
            [
                'name' => 'Presentations',
                'slug' => 'presentations',
                'description' => 'Presentation files and slides',
                'icon' => 'bi-file-slides',
                'color' => 'secondary',
                'sort_order' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Miscellaneous documents',
                'icon' => 'bi-folder',
                'color' => 'dark',
                'sort_order' => 6,
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            DocumentCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
