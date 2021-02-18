<?php

namespace App\Http\Repository;

use App\Http\Contracts\UserRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Soft delete a book
     *
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->findOne($id)
            ->first()
            ->delete();
    }

    /**
     * Update a book record
     *
     * @param int $id
     * @param array $attributes
     * @return bool|mixed
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->book->update(['id' => $id], $attributes);
    }

    /**
     * Find a book by id
     *
     * @param int $id
     * @return mixed
     */
    public function findOne(int $id)
    {
        return $this->book->find($id);
    }

    /**
     * Get the model query builder
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->book->newQuery();
    }
}