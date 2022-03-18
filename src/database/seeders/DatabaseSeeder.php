<?php

namespace Database\Seeders;

use App\Models\Subscriber;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // AdminSeeder::class,,
            // UserSeeder::class,
            Subscriber::class
        ]);
    }
}
