<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Before adding new tests in this class, please consider refreshDatabase issue
 * and test dependencies.
 */
class ApiV1AuthorizationTest extends TestCase
{
    protected $securedUri = 'api/v1/publishers/list';
    
    /**
     * @test
     */
    public function shouldFailAccessing()
    {
        $response = $this->getJson($this->securedUri);
        $response->assertStatus(401);
    }
    
    /**
     * @test
     */
    public function shouldSuccedAccessingWithPassportHelper()
    {
        Passport::actingAs(
            factory(User::class)->make()
        );
        
        $response = $this->getJson($this->securedUri);
        $response->assertStatus(200);
    }
    
    /**
     * Creating user for tests (will be deleted after tests)
     */
    protected function createUser()
    {
        $user = factory(User::class)->create([
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertIsInt(
            @$user->id, // '@' in case $user is not an object
            'User was badly created in method: ' . __METHOD__
        );
        
        return $user;
    }
    
    /**
     * Creating client for User, will be deleted after tests
     * @param User $user
     */
    protected function createClientForUser(User $user)
    {
        // creating passport grant type client
        $clients = new \Laravel\Passport\ClientRepository();
        
        $client = $clients->createPasswordGrantClient(
            $user->id,
            'test-user-jwt',
            'http://example.com'
        );
        
        $this->assertInstanceOf(
            '\Laravel\Passport\Client',
            $client,
            'The client was not created in method: ' . __METHOD__
        );
        
        return $client;
    }
    
    /**
     * @test
     */
    public function shouldGetToken()
    {
        try {
            $user = $this->createUser();
            $client = $this->createClientForUser($user);
        } catch (ExpectationFailedException $ex) {
            $this->refreshDatabase();
            $this->markTestSkipped(
                'Preconditions failed: '
                . PHP_EOL
                . $ex->getMessage()
            );
        }
        
        $response = $this->post(
            '/oauth/token',
            [
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'username' => $user->name,
                'password' => 'password',
                'scope' => '',
            ],
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        );
        
        $response->assertStatus(200);
        
        return $response->decodeResponseJson()['access_token'];
    }
    
    /**
     * @test
     */
    public function shouldNotGetTokenBecauseOfBadUserData()
    {
        $response = $this->post(
            '/oauth/token',
            [
                'grant_type' => 'password',
                'client_id' => 14,
                'client_secret' => 14,
                'username' => 'non-existing-user',
                'password' => 'non-existing-password',
                'scope' => '',
            ],
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]
        );
        
        $response->assertStatus(401);
    }
    
    /**
     * @test
     */
    public function shouldFailAccessingBecauseOfBadToken()
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer ' . 'bad-token')
            ->getJson($this->securedUri)
        ;
        
        $response->assertStatus(401);
    }
    
    /**
     * @test
     * @depends shouldGetToken
     */
    public function shouldAccessWithToken($token)
    {
        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson($this->securedUri)
        ;
        
        $response->assertStatus(200);
        
        $this->refreshDatabase();
    }
    
    /**
     * tearDownAfterClass seems not to work on Laravel
     * @see https://github.com/laravel/framework/issues/21088
     */
    protected function refreshDatabase()
    {
        Artisan::call('migrate:refresh --env=testing');
    }
}
