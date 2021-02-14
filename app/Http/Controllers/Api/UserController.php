<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class UserController extends BaseController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * This method gets all the users
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->sendResponse(
            $this->userRepository->getAll()
        );
    }
}
