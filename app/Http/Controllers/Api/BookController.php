<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\BookRepository;
use App\Http\Repository\UserRepository;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

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
    public function getAll()
    {
        return $this->bookRepository->getAll();
    }
}
