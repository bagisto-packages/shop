<?php

namespace BagistoPackages\Shop\Seeds;

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
        $this->call(CategoryTableSeeder::class);
        $this->call(InventoryTableSeeder::class);
        $this->call(LocalesTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CountryStateTranslationSeeder::class);
        $this->call(ChannelTableSeeder::class);
        $this->call(ConfigTableSeeder::class);
        $this->call(AttributeFamilyTableSeeder::class);
        $this->call(AttributeGroupTableSeeder::class);
        $this->call(AttributeTableSeeder::class);
        $this->call(AttributeOptionTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(CustomerGroupTableSeeder::class);
        $this->call(CMSPagesTableSeeder::class);
    }
}
