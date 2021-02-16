<?php

namespace App\Http\Controllers\Api;

use App\Http\Repository\UserRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

            return $this->sendResponse($user->toArray(), Response::HTTP_CREATED);
        } catch (Exception $exception) {
           return $this->sendError($exception->getMessage());
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = $this->userRepository->findOne($id);

        if (null === $user) {
            return $this->sendError('User not found');
        }

        $validator = $this->getUpdateUserValidator($request);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $this->userRepository->update($id, $request->all());

        return $this->sendResponse([], Response::HTTP_OK);
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getUpdateUserValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|unique:users|max:255',
            'email' => 'sometimes|email|unique:users|max:255',
            'password' => 'sometimes|alpha_num|min:8',
            'date_of_birth' => 'sometimes|date|date_format:Y-m-d'
        ]);
        return $validator;
    }
}
