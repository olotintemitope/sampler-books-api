<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\AuthorizationTrait;
use Tests\TestCase;

/**
 * Class UserControllerTest
 * @package Tests\Unit
 */
class UserControllerTest extends TestCase
{
    public const PASSWORD = '27kdN3r8A%X%';

    use RefreshDatabase;
    use AuthorizationTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testSeeRouteTest(): void
    {
        $res = $this->json('GET', route('api.user_all'));

        self::assertEquals(Response::HTTP_OK, $res->getStatusCode());
    }

    public function testThatTheEndpointReturnsUsers(): void
    {
        $this->seed();

        $res = $this->json('GET', route('api.user_all'));

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'date_of_birth',
                    ]
                ]
            ]);
    }

    public function testThatUserCannotRegisterWithoutProvingDetails(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_create'),
            $this->getUserPostData(),
            $headers
        );
        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals("The name field is required.", $data->name[0]);
        self::assertEquals("The email field is required.", $data->email[0]);
        self::assertEquals("The password field is required.", $data->password[0]);
        self::assertEquals("The date of birth field is required.", $data->date_of_birth[0]);
    }

    public function testThatEmailSuppliedIsInvalid(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_create'),
            $this->getUserPostData(
                'testing',
                'Sampler User 1',
                ucwords(bin2hex(random_bytes(10))),
                now()->format('Y-m-d')
            ),
            $headers
        );
        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals("The email must be a valid email address.", $data->email[0]);
    }

    public function testThatRequestWithInvalidDateFormatWillBeRejected(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_create'),
            $this->getUserPostData(
                'testing@sampler.com',
                'Sampler User 1',
                'Laztop11',
                now()->format('Y/m/d')
            ), $headers);

        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals('The date of birth does not match the format Y-m-d.', $data->date_of_birth[0]);
    }

    public function testThatInvalidPasswordWillBeRejected(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_create'),
            $this->getUserPostData(
                'testing@sampler.com',
                'Sampler User 1',
                '$$lazopoty',
                now()->format('Y-m-d')
            ), $headers);

        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals("The password format is invalid.", $data->password[0]);
    }

    public function testThatUserCanRegister(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.user_create'),
            $this->getUserPostData(
                'testing@sampler.com',
                'Sampler User',
                self::PASSWORD,
                now()->addDays(10)->format('Y-m-d')
            ), $headers);

        $res->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'date_of_birth',
                ]
            ]);
    }

    public function testThatYouCanGetASingleUser(): void
    {
        $user = factory(User::class)->create();

        $res = $this->json(
            'GET',
            route('api.user_find', ['id' => $user->id]));

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'date_of_birth',
                ]
            ]);

        $content = json_decode($res->getContent());

        self::assertTrue($content->success);
    }

    public function testThatUserCanUpdateTheirDetails(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        $res = $this->json(
            'PUT',
            route('api.user_update', ['id' => $user->id]),
            $this->getUserPostData(
                'testing@sampler.com',
                'Sampler User 2',
                self::PASSWORD,
                now()->addDays(15)->format('Y-m-d')
            ), $headers);

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                ]
            ]);
    }

    public function testThatUserUpdateWithoutRecordWillThrowAnError(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'PUT',
            route('api.user_update', ['id' => 1000]),
            $this->getUserPostData(
                'testing@sampler.com',
                'Sampler User 3',
                ucwords(bin2hex(random_bytes(10))),
                now()->addDays(25)->format('Y-m-d')
            ), $headers);

        $content = json_decode($res->getContent());

        $res->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
        self::assertEquals("User not found", $content->message);
    }

    public function testThatPartialUpdateWorks(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        $res = $this->json(
            'PUT',
            route('api.user_update', ['id' => $user->id]), [
            'email' => 'testing@sampler.com',
            'name' => 'Sampler User 4'
        ], $headers);

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                ]
            ]);

        $content = json_decode($res->getContent());

        self::assertTrue($content->success);
    }

    public function testThatUserWithoutTokenCannotRegisterTheirDetails(): void
    {
        $res = $this->json(
            'POST',
            route('api.user_create'),
            $this->getUserPostData(
                'testing@sampler.com',
                'Sampler User',
                ucwords(bin2hex(random_bytes(10))),
                now()->addDays(10)->format('Y-m-d')
            ));

        $res->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testThatUserWithoutTokenCannotSoftDeleteTheirData(): void
    {
        $user = factory(User::class)->create();

        $res = $this->json(
            'DELETE',
            route('api.user_delete', ['id' => $user->id]), [
        ]);

        self::assertEquals($res->getStatusCode(), Response::HTTP_UNAUTHORIZED);
    }

    public function testThatUserWithWrongIdCannotBeSoftDeleted(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'DELETE',
            route('api.user_delete', ['id' => 2000]), [
        ], $headers);

        self::assertEquals($res->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    public function testThatUserWithTokenBeSoftDeleted(): void
    {
        $headers = $this->authorizeUser();
        $user = factory(User::class)->create();

        $res = $this->json(
            'DELETE',
            route('api.user_delete', ['id' => $user->id]), [
        ], $headers);

        self::assertEquals($res->getStatusCode(), Response::HTTP_OK);
    }

    /**
     * @param string $email
     * @param string $name
     * @param string $password
     * @param string $dateOfBirth
     * @return string[]
     */
    protected function getUserPostData($email = '', $name = '', $password = '', $dateOfBirth = ''): array
    {
        return [
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'date_of_birth' => $dateOfBirth,
        ];
    }

    public function tearDown(): void
    {
        try {
            parent::tearDown();
        } catch (\Throwable $e) {
        }
    }
}
