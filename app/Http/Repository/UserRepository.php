<?php

namespace App\Http\Repository;
use App\Http\Contracts\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getAll()
    {
        return $this->user::all();
    }

    public function create(array $attributes)
    {
        return $this->user->create($attributes);
    }

    public function update(int $id, array $attributes): bool
    {
        return $this->user->update(['id' => $id], $attributes);
    }

    public function findOne(int $id)
    {
       return $this->user->find($id);
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }
}