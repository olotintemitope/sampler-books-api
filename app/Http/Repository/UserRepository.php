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
     * @return User[]|Collection
     */
    public function getAll()
    {
        return $this->user::all();
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes)
    {
        return $this->user->create($attributes);
    }

    /**
     * @param int $id
     * @param array $attributes
     * @return bool
     */
    public function update(int $id, array $attributes): bool
    {
        return $this->user->update(['id' => $id], $attributes);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function findOne(int $id)
    {
       return $this->user->find($id);
    }

    /**
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