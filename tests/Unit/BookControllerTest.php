<?php

namespace Tests\Unit;

use Illuminate\Http\Response;
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

        self::assertEquals(Response::HTTP_OK, $res->getStatusCode());
    }

    public function testThatTheEndpointReturnsBooks(): void
    {
        $this->seed();

        $res = $this->json('GET', route('api.book_all'));

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                    [
                        'id',
                        'title',
                        'isbn',
                        'published_at',
                        'status',
                    ]
                ]
            ]);

    }

    public function testThatBookCannotBeCreatedWithEmptyDetails(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.book_create'),
            $this->getBookPostData(),
            $headers
        );
        $content = json_decode($res->getContent());
        $data = $content->data;
    }

    /**
     * Set book data
     *
     * @param string $title
     * @param string $isbn
     * @param string $publishedAt
     * @param string $status
     * @return string[]
     */
    protected function getBookPostData($title = '', $isbn = '', $publishedAt = '', $status = ''): array
    {
        return [
            'title' => $title,
            'isbn' => $isbn,
            'published_at' => $publishedAt,
            'status' => $status,
        ];
    }
}
