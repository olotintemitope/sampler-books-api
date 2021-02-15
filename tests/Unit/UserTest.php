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

    public function testThatUserCanRegister(): void
    {
        $data = [
            'email' => '',
            'name' => '',
            'password' => '',
            'date_of_birth' => '',
        ];

        $res = $this->json('POST', route('api.user_create'), $data);
    }
}
