<?php
use Illuminate\Database\Seeder;
use App\Models\Synonym;

class SynonymSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Synonym::truncate();
        factory(Synonym::class, 100)->create();
    }
}