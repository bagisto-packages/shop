<?php

namespace BagistoPackages\Shop\Seeds;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class LocalesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('channels')->delete();

        DB::table('locales')->delete();

        DB::table('locales')->insert([
            [
                'id' => 1,
                'code' => 'en',
                'name' => 'English',
            ]
        ]);
    }
}
