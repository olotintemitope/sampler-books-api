<?php

namespace App\Http\Controllers\Api;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use stdClass;

/**
 * Class AccessTokenController
 * @package App\Http\Controllers\Api
 */
class AccessTokenController extends BaseController
{
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
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email"    => "required|email|exists:users",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $credentials = $this->credentials();

        try {
            $response = $this->httpClient->post('/oauth/token', [
                'form_params' => [
                    'client_id'     => $credentials->client_id,
                    'client_secret' => $credentials->client_secret,
                    'grant_type'    => $credentials->grant,
                    'username'      => $request->get('email'),
                    'password'      => $request->get('password'),
                    'scopes'        => '[*]',
                ]
            ]);
        } catch (Exception $exception) {
            if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                return response(['message' => 'Invalid user credentials'], 401);
            }

            throw $exception;
        }

        return $this->sendResponse($response);
    }

    /**
     * Return credentials from cache or database if not set.
     *
     * @return object
     */
    private function credentials(): object
    {
        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();

        $response = new stdClass();
        $response->grant = 'password';
        $response->client_id = $client->id;
        $response->client_secret = $client->secret;

        return $response;
    }
}
