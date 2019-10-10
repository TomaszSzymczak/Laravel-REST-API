<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Middleware\Authenticate;
use App\Magazine;
use App\Publisher;

class SearchMagazinesTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @var string */
    protected $searchUri = 'api/v1/magazines/search';
    
    public function setUp(): void {
        parent::setUp();
        
        $this->withoutMiddleware(Authenticate::class);
    }

    public function testGetMagazines()
    {
        $publisher = factory(Publisher::class)->create();
        factory(Magazine::class, 40)->create(['publisher_id' => $publisher->id]);
        
        $response = $this->getJson($this->searchUri);
        
        $response->assertStatus(200);
        $response->assertJsonCount(40, 'data');
        
        $response = $this->getJson($this->searchUri . '?per_page=20');
        $response->assertStatus(200);
        $response->assertJsonCount(20, 'data');
        
        $response = $this->getJson($this->searchUri . '?per_page=20&page=2');
        $response->assertStatus(200);
        $response->assertJsonCount(20, 'data');
    }
    
    public function testSearchForSpecificMagazines()
    {
        factory(Publisher::class, 10)->create()->each(
            function($publisher) {
                factory(Magazine::class, rand(5, 15))->create([
                    'publisher_id' => $publisher->id
                ]);
            }
        );
        
        $magazines = Magazine::all();
        
        $magazinesByPublisher = $magazines->mapToGroups(function($mag){
            return [$mag->publisher_id => $mag];
        });
        
        $onePublisherMagazines = $magazinesByPublisher->random();
        $publisherId = $onePublisherMagazines->first()->publisher_id;
        
        $response = $this->getJson(
            $this->searchUri . '?publisher_id=' . $publisherId
        );
        
        $response->assertStatus(200);
        $response->assertJsonCount(count($onePublisherMagazines), 'data');
        
        $publishers = factory(Publisher::class, 2)->create()->each(
            function($publisher) {
                $publisher->magazines()->saveMany(
                    factory(Magazine::class, 15)->make([
                        'name' => $this->faker->word()
                                  . '[search_phrase]'
                                  . $this->faker->word()
                    ])
                );
            }
        );
        
        $response = $this->getJson($this->searchUri . '?name_part=[search_phrase]');
        $response->assertStatus(200);
        $response->assertJsonCount(30, 'data');
        
        $response = $this->getJson(
            $this->searchUri
            . '?name_part=[search_phrase]'
            . '&publisher_id=' . $publishers->first()->id
        );
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
    }
}
