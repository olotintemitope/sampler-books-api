<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\User;
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

        self::assertFalse($content->success);
        self::assertEquals("The title field is required.", $data->title[0]);
        self::assertEquals("The isbn field is required.", $data->isbn[0]);
        self::assertEquals("The published at field is required.", $data->published_at[0]);
        self::assertEquals("The status field is required.", $data->status[0]);
    }

    public function testThatIsbnSuppliedIsInvalid(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.book_create'),
            $this->getBookPostData(
                'The heros of sampler',
                '1234567890',
                now()->format('Y-m-d'),
                'CHECKED_OUT'
            ),
            $headers
        );
        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals("The selected isbn is invalid.", $data->isbn[0]);
    }

    public function testThatRequestWithInvalidDateFormatWillBeRejected(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.book_create'),
            $this->getBookPostData(
                'The heroes of sampler',
                '0441013597',
                now()->format('Y/m/d'),
                'CHECKED_OUT'
            ),
            $headers
        );
        $content = json_decode($res->getContent());
        $data = $content->data;

        self::assertFalse($content->success);
        self::assertEquals('The published at does not match the format Y-m-d.', $data->published_at[0]);
    }

    public function testThatUserCanCreateBook(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'POST',
            route('api.book_create'),
            $this->getBookPostData(
                'The heroes of sampler dome',
                '0441013597',
                now()->format('Y-m-d'),
                'CHECKED_OUT'
            ),
            $headers
        );
        $content = json_decode($res->getContent());
        $data = $content->data;

        $res->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'title',
                    'isbn',
                    'published_at',
                    'status',
                ]
            ]);
    }

    public function testUpdateBookDetails(): void
    {
        $headers = $this->authorizeUser();

        $book = factory(Book::class)->create();

        $res = $this->json(
            'PUT',
            route('api.book_update', ['id' => $book->id]),
            $this->getBookPostData(
                'The kings of sampler dome',
                '0593139135',
                now()->addDays(50)->format('Y-m-d'),
                'AVAILABLE'
            ),
            $headers
        );

        $res->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'data' => [
                ]
            ]);

        $content = json_decode($res->getContent());

        self::assertTrue($content->success);
    }

    public function testPartiallyUpdateBookDetails(): void
    {
        $headers = $this->authorizeUser();

        $book = factory(Book::class)->create();

        $res = $this->json(
            'PUT',
            route('api.book_update', ['id' => $book->id]),
            [
                'title' => 'The kings of sampler dome',
                'status' => 'CHECKED_OUT',
            ],
            $headers
        );

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
        $book = factory(Book::class)->create();

        $res = $this->json(
            'PUT',
            route('api.book_update', ['id' => $book->id]),
            [
                'title' => 'The kings of sampler dome',
                'status' => 'CHECKED_OUT',
            ]
        );

        $res->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testThatBookWithoutTokenCannotSoftDeleteTheirData(): void
    {
        $book = factory(Book::class)->create();

        $res = $this->json(
            'DELETE',
            route('api.book_delete', ['id' => $book->id]));

        self::assertEquals($res->getStatusCode(), Response::HTTP_UNAUTHORIZED);
    }

    public function testThatBookWithWrongIdCannotBeSoftDeleted(): void
    {
        $headers = $this->authorizeUser();

        $res = $this->json(
            'DELETE',
            route('api.book_delete', ['id' => 2000]), [
        ], $headers);

        self::assertEquals($res->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    public function testThatBookWithAValidIdCanBeSoftDeleted(): void
    {
        $headers = $this->authorizeUser();
        $book = factory(Book::class)->create();

        $res = $this->json(
            'DELETE',
            route('api.book_delete', ['id' => $book->id]), [
        ], $headers);

        self::assertEquals($res->getStatusCode(), Response::HTTP_OK);
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
