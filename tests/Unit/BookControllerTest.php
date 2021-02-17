<?php

namespace Tests\Unit;

use Tests\AuthorizationTrait;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;
    use AuthorizationTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testSeeRoute(): void
    {
        $res = $this->json('GET', route('api.book_all'));
    }
}
