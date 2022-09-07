<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('teams')->insert([
            'name' => 'Corinthians',
        ]);

        DB::table('teams')->insert([
            'name' => 'Palmeiras',
            //id 2
        ]);
        DB::table('teams')->insert([
            'name' => 'Vasco',
            //id 3
        ]);
        DB::table('teams')->insert([
            'name' => 'Flamengo',
            //id 4
        ]);
        DB::table('teams')->insert([
            'name' => 'Cruzeiro',
            //id 5
        ]);
        DB::table('teams')->insert([
            'name' => 'Bahia',
            //id 6
        ]);
        DB::table('teams')->insert([
            'name' => 'Vitoria',
            //id 7
        ]);
        DB::table('teams')->insert([
            'name' => 'Internacional',
            //id 8
        ]);
        DB::table('teams')->insert([
            'name' => 'Vila Tião',
            //id 9
        ]);
        DB::table('teams')->insert([
            'name' => 'Altinopolis',
            //id 10
        ]);
        DB::table('teams')->insert([
            'name' => 'Rexona Team',
            //id 11
        ]);


        DB::table('championships')->insert([
            'name' => 'Libertadores',
            //id 1
        ]);
    }
}
