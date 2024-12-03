<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle JSON-specific responses
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        // Use parent handler for non-JSON requests
        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions and return consistent JSON responses.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->validationExceptionResponse($exception);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->authenticationExceptionResponse();
        }

        // General error response
        return response()->json([
            'success' => false,
            'message' => $this->getErrorMessage($exception),
            'data' => []
        ], $this->getStatusCode($exception));
    }

    /**
     * Return a JSON response for validation exceptions.
     *
     * @param \Illuminate\Validation\ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    private function validationExceptionResponse(ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $exception->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return a JSON response for authentication exceptions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function authenticationExceptionResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Get a custom error message from the exception.
     *
     * @param \Throwable $exception
     * @return string
     */
    private function getErrorMessage(Throwable $exception)
    {
        return config('app.debug') 
            ? $exception->getMessage() 
            : 'Something went wrong';
    }

    /**
     * Get the appropriate HTTP status code for the exception.
     *
     * @param \Throwable $exception
     * @return int
     */
    private function getStatusCode(Throwable $exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return $exception->getCode() && is_int($exception->getCode())
            ? $exception->getCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // You can log or handle specific exceptions here
        });
    }
}
