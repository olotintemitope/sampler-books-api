<?php

namespace Tests\Unit;

use Illuminate\Http\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic route test.
     *
     * @return void
     */
    public function testSeeRouteTest(): void
    {
        $res = $this->json('GET', route('api.user_all'));

        self::assertEquals(Response::HTTP_OK, $res->getStatusCode());
    }

    public function testThatTheEndpointReturnsUsers(): void
    {
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
        $res = $this->json('POST', route('api.user_create'), $this->getUserPostData());
        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals("The name field is required.", $data->name[0]);
        self::assertEquals("The email field is required.", $data->email[0]);
        self::assertEquals("The password field is required.", $data->password[0]);
        self::assertEquals("The date of birth field is required.", $data->date_of_birth[0]);
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
}
