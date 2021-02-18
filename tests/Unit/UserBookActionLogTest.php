<?php


namespace Tests\Unit;


use App\Models\User;
use App\Models\UserActionLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $userActionLogs = factory(UserActionLog::class, 5)
            ->create(['user_id' => $user->id]);

        $bookIds = $userActionLogs->pluck('book_id')->toArray();

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

    /**
     * @param array $books
     * @return string[]
     */
    protected function getUserBooksPostData($books = []): array
    {
        return ['books' => $books];
    }
}