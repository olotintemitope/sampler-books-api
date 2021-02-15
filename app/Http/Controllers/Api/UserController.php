<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
                ->toArray()
        );
    }

    public function create(Request $request)
    {

    }
}
