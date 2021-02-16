<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\UserRepository;
use Exception;
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

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);

        try {
            $user = $this->userRepository->create($data);
            $userToken = $user->createToken('Sampler')->accessToken;
            $user->token = $userToken;

            return $this->sendResponse($user->toArray());
        } catch (Exception $exception) {
           return $this->sendError($exception->getMessage());
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
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|alpha_num|min:8',
            'date_of_birth' => 'required|date|date_format:Y-m-d'
        ]);
    }
}
