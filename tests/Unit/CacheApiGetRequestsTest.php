<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;

class CacheApiGetRequestsTest extends TestCase
{
    /** @var string */
    protected $publishersUri = 'api/v1/publishers/list';
    
    public function testCheckIfCacheApplies()
    {
        $middleware = new \App\Http\Middleware\CacheApiGetRequests;
        
        $getRequest = Request::create(
            $this->publishersUri,
            'GET'
        );
        
        $this->assertTrue(
            $middleware->checkIfCacheApplies($getRequest)
        );
        
        $postRequest = Request::create(
            'api/v1/doesnt-matter',
            'POST'
        );
        
        $this->assertFalse(
            $middleware->checkIfCacheApplies($postRequest)
        );
    }
}
