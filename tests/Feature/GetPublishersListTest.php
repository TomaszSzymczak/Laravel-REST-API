<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Publisher;
use App\Http\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Assuming that authentication and authorization is on middleware level.
 */
class GetPublishersListTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void {
        parent::setUp();
        
        factory(Publisher::class, 20)->create();
        $this->withoutMiddleware(Authenticate::class);
    }
    
    public function testGetPublishersList()
    {
        $response = $this->getJson('api/v1/publishers/list');
        
        $response->assertStatus(200);
        $response->assertJsonCount(20, 'data');
    }
    
    public function testGetPublishersListWithPagination()
    {
        $response = $this->getJson('api/v1/publishers/list?per_page=10');
        
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        
        $response->assertJsonPath(
            'meta.path',
            url('/api/v1/publishers/list?per_page=10')
        );
        
        $response->assertJsonPath(
            'links.first',
            url('/api/v1/publishers/list?per_page=10&page=1')
        );
        
        $response->assertJsonPath(
            'links.next',
            url('/api/v1/publishers/list?per_page=10&page=2')
        );
        
        $response->assertJsonPath(
            'links.prev',
            null
        );
        
        $response->assertJsonPath(
            'links.last',
            url('/api/v1/publishers/list?per_page=10&page=2')
        );
    }
    
    public function testPaginationPage2()
    {
        $response = $this->getJson('api/v1/publishers/list?per_page=19&page=2');
        
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
