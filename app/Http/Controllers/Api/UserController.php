<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = $this->getCreateUserValidator($request);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getCreateUserValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|alpha_num|min:8',
            'date_of_birth' => 'required|date'
        ]);
    }
}
