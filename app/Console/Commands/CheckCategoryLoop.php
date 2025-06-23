<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class CheckCategoryLoop extends Command
{
    protected $signature = 'check:category-loop';
    protected $description = 'Check for recursive category loops';

    public function handle()
    {
        $categories = Category::all();
        $visited = [];

        foreach ($categories as $cat) {
            $id = $cat->id;
            $seen = [$id];

            while ($cat->parent_id) {
                if (in_array($cat->parent_id, $seen)) {
                    $this->error("❌ Loop detected: " . implode(' -> ', $seen) . " -> " . $cat->parent_id);
                    break;
                }

                $seen[] = $cat->parent_id;
                $cat = $categories->firstWhere('id', $cat->parent_id);
                if (!$cat) break;
            }
        }

        $this->info("✅ Done checking categories.");
    }
}
