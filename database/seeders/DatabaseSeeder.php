<?php

// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AhpCriteriaSeeder::class,
            FuzzyRulesSeeder::class,
            MasterDataSeeder::class,
            // BalitaSeeder::class,
        ]);
    }
}