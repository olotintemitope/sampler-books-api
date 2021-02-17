<?php


namespace Tests\Unit;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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
        $user = factory(User::class)->create();

        $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_login'), [
            'email' => $user->email,
            'password' => $user->password
        ]);

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                        'id',
                        'name',
                        'email',
                        'date_of_birth',
                        'token',
                    ]

            ]);
    }

    public function tearDown(): void
    {
        try {
            parent::tearDown();
        } catch (\Throwable $e) {
        }
    }
}