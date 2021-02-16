<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

/**
 * Class BaseController
 */
class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @param array $result
     * @param int $statusCode
     * @return JsonResponse
     */
    public function sendResponse(array $result, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
        ];

        return response()->json($response, $statusCode);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError(string $error, $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}