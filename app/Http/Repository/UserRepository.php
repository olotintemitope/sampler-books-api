<?php

namespace App\Http\Repository;
use App\Http\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserRepository
 * @package App\Http\Repository
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get all users
     *
     * @return User[]|Collection
     */
    public function getAll()
    {
        return $this->user::all();
    }

    /**
     * Create a new user
     *
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->user->create($attributes);
    }

    /**
     * Update an existing user
     *
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->user->update(['id' => $id], $attributes);
    }

    /**
     * Get a single user by id
     *
     * @param int $id
     * @return mixed
     */
    public function findOne(int $id)
    {
       return $this->user->find($id);
    }

    /**
     * Soft delete a user
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
}