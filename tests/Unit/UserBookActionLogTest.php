<?php


namespace Tests\Unit;


use App\Models\Book;
use App\Models\User;
use App\Models\UserActionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\AuthorizationTrait;
use Tests\TestCase;

class UserBookActionLogTest extends TestCase
{
    use RefreshDatabase;
    use AuthorizationTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:install');
    }

    public function testThatUserCannotCheckInBooks(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        $res = $this->json(
            'POST',
            route('api.user_book_checkin', ['id' => $user->id]),
            $this->getUserBooksPostData(),
            $headers
        );
        $content = json_decode($res->getContent());

        self::assertFalse($content->success);
        self::assertEquals($content->data->books[0], "The books field is required.");
    }

    public function testThatUserCanCheckInBooks(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        factory(UserActionLog::class, 3)->create(
            [
                'user_id' => $user->id,
            ]
        );

        $bookIds = factory(Book::class, 3)->create()->pluck('id')->toArray();

        $res = $this->json(
            'POST',
            route('api.user_book_checkin', ['id' => $user->id]),
            $this->getUserBooksPostData($bookIds),
            $headers
        );

        $res->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'data' => [
                ]
            ]);

        $content = json_decode($res->getContent());

        self::assertTrue($content->success);
    }

    public function testThatUserCanCheckOutBooks(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        factory(UserActionLog::class, 3)->create(
            [
                'user_id' => $user->id,
            ]
        );

        $bookIds = factory(Book::class, 3)->create()->pluck('id')->toArray();

        $res = $this->json(
            'POST',
            route('api.user_book_checkout', ['id' => $user->id]),
            $this->getUserBooksPostData($bookIds),
            $headers
        );

        $res->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'success',
                'data' => [
                ]
            ]);

        $content = json_decode($res->getContent());

        self::assertTrue($content->success);
    }

    public function testThatBooksWithInvalidIdsCannotBeCheckedIn(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        factory(UserActionLog::class, 3)->create(
            [
                'user_id' => $user->id,
            ]
        );

        $res = $this->json(
            'POST',
            route('api.user_book_checkin', ['id' => $user->id]),
            $this->getUserBooksPostData([200, 500]),
            $headers
        );

        $content = json_decode($res->getContent());

        self::assertEquals($content->data->{0}, "Book with ID: 200 not found");
        self::assertEquals($content->data->{1}, "Book with ID: 500 not found");

    }

    public function testThatBooksWithInvalidIdsCannotBeCheckedOut(): void
    {
        $headers = $this->authorizeUser();

        $user = factory(User::class)->create();

        factory(UserActionLog::class, 3)->create(
            [
                'user_id' => $user->id,
            ]
        );

        $res = $this->json(
            'POST',
            route('api.user_book_checkout', ['id' => $user->id]),
            $this->getUserBooksPostData([40, 80]),
            $headers
        );

        $content = json_decode($res->getContent());

        self::assertEquals($content->data->{0}, "Book with ID: 40 not found");
        self::assertEquals($content->data->{1}, "Book with ID: 80 not found");

    }

    /**
     * @param array $books
     * @return string[]
     */
    protected function getUserBooksPostData($books = []): array
    {
        return ['books' => $books];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}