<?php


namespace Tests\Unit;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\AuthorizationTrait;
use Tests\TestCase;

/**
 * Class AuthenticationTest
 * @package Tests\Unit
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use AuthorizationTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testThatUserCanLogin(): void
    {
        $this->authorizeUser([
            'email' => 'testing@sampler2.com',
            'password' => 'Lazopoty02'
        ]);

        $res = $this->json(
            'POST',
            route('api.user_login'), [
                'email' => 'testing@sampler2.com',
                'password' => 'Lazopoty02',
                ]);

        dd($res->content());
    }

    public function tearDown(): void
    {
        try {
            parent::tearDown();
        } catch (\Throwable $e) {
        }
    }
}