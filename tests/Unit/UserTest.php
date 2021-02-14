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

    public function testThatTheEndpointReturnsUsers()
    {
        $res = $this->json('GET', route('api.user_all'));
    }
}
