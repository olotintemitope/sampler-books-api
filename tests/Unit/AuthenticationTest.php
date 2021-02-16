<?php


namespace Tests\Unit;


use Tests\TestCase;

/**
 * Class AuthenticationTest
 * @package Tests\Unit
 */
class AuthenticationTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testThatUserCanLogin(): void
    {
        $res = $this->json(
            'POST',
            route('api.user_create'),
            []);
    }

    public function tearDown(): void
    {
        try {
            parent::tearDown();
        } catch (\Throwable $e) {
        }
    }
}