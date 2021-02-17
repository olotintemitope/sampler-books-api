<?php

namespace App\Http\Repository;

use App\Http\Contracts\UserRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BookRepository
 * @package App\Http\Repository
 */
class BookRepository implements UserRepositoryInterface
{
    /**
     * @var Book
     */
    private $book;

    /**
     * BookRepository constructor.
     * @param Book $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Return all the books
     *
     * @return Book[]|Collection|mixed
     */
    public function getAll()
    {
        return $this->book::all();
    }

    /**
     * Create a new book
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->book->create($attributes);
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(int $id, array $attributes)
    {
        // TODO: Implement update() method.
    }

    public function findOne(int $id)
    {
        // TODO: Implement findOne() method.
    }
}