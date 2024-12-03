<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Exception;

class ApiController extends Controller
{
    protected $httpStatusCode = Response::HTTP_OK;

    protected function successResponse($data, $message = null, $statusCode = Response::HTTP_OK): JsonResponse
    {
        $this->httpStatusCode = $statusCode;

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $this->httpStatusCode);
    }

    protected function errorResponse($message, $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        $this->httpStatusCode = $statusCode;

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => [],
        ], $this->httpStatusCode);
    }

    public function handleException(Exception $exception): JsonResponse
    {
        $this->httpStatusCode = $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;

        return $this->errorResponse($exception->getMessage(), $this->httpStatusCode);
    }
}