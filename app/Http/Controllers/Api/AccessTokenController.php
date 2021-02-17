<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\ValidationTrait;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class AccessTokenController
 * @package App\Http\Controllers\Api
 */
class AccessTokenController extends BaseController
{
    use ValidationTrait;

    /**
     * @var Client $httpClient
     */
    private $httpClient;

    /**
     * AccessTokenController constructor.
     */
    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => config('app.url'),
        ]);
    }

    /**
     * Login and return token back to the user
     *
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function login(Request $request)
    {
        $validator = $this->getUserLoginValidator($request);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserByEmailAndPassword($request);
        if (null === $user) {
            return $this->sendError('User not registered, you need to register', [], Response::HTTP_BAD_REQUEST);
        }

        $accessToken = $user->createToken('sam')->accessToken;
        $user->token = $accessToken;

        return $this->sendResponse($user->toArray());
    }

    /**
     * Log the user out and remove all the access tokens
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $validator = $this->getUserEmailValidator($request);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUserByEmail($request);

        if (null === $user) {
            return $this->sendError('User not found, you need to register', [], Response::HTTP_BAD_REQUEST);
        }

        $user->tokens()->each(static function($token) {
            $token->revoke();
        });

        return $this->sendResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function getUserByEmailAndPassword(Request $request)
    {
        return User::ofEmailAndPassword($request->email, $request->password);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function getUserByEmail(Request $request)
    {
        return User::ofEmail($request->email);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getUserLoginValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            "email" => "required|email|exists:users",
            "password" => $this->getPasswordValidation(),
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function getUserEmailValidator(Request $request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($request->all(), [
            "email" => "required|email|exists:users",
        ]);
    }
}
