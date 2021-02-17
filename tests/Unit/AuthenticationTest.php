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

    public function testThatUserWithoutAnExistingCredentialCannotLogin() : void
    {
        $res = $this->json(
            'POST',
            route('api.user_login'),
            [
                'email' => 'emailnot@found.com',
                'password' => ucwords(bin2hex(random_bytes(10)))
            ]);

        self::assertEquals($res->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    public function testThatUserWithCredentialsCanLogOut() : void
    {
        $user = factory(User::class)->create();
        $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_logout'), [
            'email' => $user->email,
        ]);

        self::assertEquals($res->getStatusCode(), Response::HTTP_NO_CONTENT);
    }

    public function testThatUserWithoutCredentialsCannotLogOut() : void
    {
        $res = $this->json(
            'POST',
            route('api.user_logout'), [
            'email' => 'nologin@details.com',
        ]);

        self::assertEquals($res->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    public function tearDown(): void
    {
        try {
            parent::tearDown();
        } catch (\Throwable $e) {
        }
    }
}