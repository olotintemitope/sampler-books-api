<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\BookRepository;
use App\Http\Repository\UserRepository;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class BookController extends BaseController
{
    /**
     * @var BookRepository
     */
    private $bookRepository;

    /**
     * BookController constructor.
     * @param BookRepository $bookRepository
     */
    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Get all books
     * @return Book[]|Collection|mixed
     */
    public function getAll(): JsonResponse
    {
        return $this->sendResponse(
            $this->bookRepository->getAll()
                ->toArray()
        );
    }

    public function create(Request $request)
    {
        $statuses = implode(',', ['CHECKED_OUT', 'AVAILABLE']);
        $bookNumbers = implode(',', $this->getBookNumbers());

        $validator = $this->getCreateBookValidator($request, $statuses, $bookNumbers);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();

        try {
            $book = $this->bookRepository->create($data);

            return $this->sendResponse($book->toArray(), Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Get the book validator
     *
     * @param Request $request
     * @param $statuses
     * @param $bookNumbers
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function getCreateBookValidator(Request $request, $statuses, $bookNumbers): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'title' => 'required|unique:books|max:255',
            'isbn' => "required|unique:books|in:$bookNumbers|max:10",
            'published_at' => 'required|date|date_format:Y-m-d',
            'status' => "required|string|in:$statuses",
        ]);
    }

    /**
     * Get available ISBN Numbers
     *
     * @return string[]
     */
    private function getBookNumbers(): array
    {
        return [
            '0005534186',
            '0978110196',
            '0978108248',
            '0978194527',
            '0978194004',
            '0978194985',
            '0978171349',
            '0978039912',
            '0978031644',
            '0978168968',
            '0978179633',
            '0978006232',
            '0978195248',
            '0978125029',
            '0978078691',
            '0978152476',
            '0978153871',
            '0978125010',
            '0593139135',
            '0441013597',
        ];

    }
}
