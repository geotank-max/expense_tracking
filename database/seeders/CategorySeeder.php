<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{

    public function run(): void
    {
        $user = User::first();

        if ($user) {
            $defaults = [
                ["name" => 'Food & Dining', 'type' => 'expense', 'color' => '#dc3545'],
                ["name" => 'Salary', 'type' => 'income', 'color' => '#198754'],
                ["name" => 'Utilities & Bills', 'type' => 'expense', 'color' => '#ffc107'],
                ["name" => 'Freelance', 'type' => 'income', 'color' => '#0d6efd'],
            ];

            foreach ($defaults as $cat) {
                Category::create(array_merge($cat, ['user_id' => $user->id]));
            }
        }
    }
}
