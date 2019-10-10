<?php

use Illuminate\Database\Seeder;
use App\Publisher;
use App\Magazine;


class PublishersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Publisher::class, 30)->create()->each(function(Publisher $publisher) {
            $publisher->magazines()->saveMany(
                factory(Magazine::class, rand(1,15))->make()
            );
        });
    }
}
