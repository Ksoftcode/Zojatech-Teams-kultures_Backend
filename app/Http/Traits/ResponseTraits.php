<?php


namespace App\Http\Traits;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ValidationResponseException;
use Illuminate\Http\Resources\Json\JsonResource;

trait ResponseTrait
{
    /**
     * Return a successful ok HTTP response
     *
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function okResponse(string $message, $data = null): JsonResponse
    {
        return $this->successResponse($message, $data, 200);
    }


    /**
     * Return a successful created HTTP response
     *
     * @param string $message
     * @param $data
     *
     * @return JsonResponse
     */
    public function createdResponse(string $message, $data = null): JsonResponse
    {
        return $this->successResponse($message, $data, 201);
    }

    /**
     * Return a successful no content HTTP response
     *
     * @return JsonResponse
     */
    public function noContentResponse(): JsonResponse
    {
        return $this->successResponse('', null, 204);
    }

    /**
     * Return a generic successful HTTP response
     *
     * @param string $message
     * @param $data
     * @param int $status
     *
     * @return JsonResponse
     */
    public function successResponse(string $message, $data = null, int $status = 200): JsonResponse
    {

        return $this->jsonResponse($message, $status, $data);
    }

    /**
     * Return a validation error response
     *
     * @param Validator $validator
     * @param Request|null $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    // public function validationErrorResponse(Validator $validator, Request $request = null): \Symfony\Component\HttpFoundation\Response
    // {
    //     return (new ValidationResponseException($validator, $request))
    //         ->getResponse();
    // }

    /**
     * Return an unauthenticated HTTP error response
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    public function unauthenticatedResponse(string $message): JsonResponse
    {
        return $this->clientErrorResponse($message, 401);
    }

    /**
     * Return a bad request HTTP error response
     *
     * @param string $message
     * @param array|null $error
     *
     * @return JsonResponse
     */
    public function badRequestResponse(string $message, array $error = null): JsonResponse
    {
        return $this->clientErrorResponse($message, 400, $error);
    }

    /**
     * Return a forbidden HTTP error response
     *
     * @param string $message
     * @param array|null $error
     *
     * @return JsonResponse
     */
    public function forbiddenResponse(string $message, array $error = null): JsonResponse
    {
        return $this->clientErrorResponse($message, 403, $error);
    }

    /**
     * Return a not found HTTP error response
     *
     * @param string $message
     * @param null $error
     *
     * @return JsonResponse
     */
    public function notFoundResponse(string $message, $error = null): JsonResponse
    {
        return $this->clientErrorResponse($message, 404, $error);
    }

    /**
     * Return a generic client HTTP error response
     *
     * @param string $message
     * @param int $status
     * @param null $error
     *
     * @return JsonResponse
     */
    public function clientErrorResponse(string $message, int $status = 400, $error = null): JsonResponse
    {
        return $this->jsonResponse($message, $status, $error);
    }

    /**
     * Return a generic server HTTP error response
     *
     * @param string $string
     * @param int $status
     * @param Exception|null $exception
     *
     * @return JsonResponse
     */
    public function serverErrorResponse(string $string, int $status = 503, Exception $exception = null): JsonResponse
    {
        if ($exception !== null) {
            $error = "{$exception->getMessage()}
            on line {$exception->getLine()}
            in {$exception->getFile()}";

            Log::error($error);
            Log::channel('slack')->error($error);
        }

        return $this->jsonResponse($string, $status);
    }

    /**
     * Return a generic HTTP response
     *
     * @param string $message
     * @param int $status
     * @param null $data
     * @return JsonResponse
     */
    public function jsonResponse(string $message, int $status, $data = null): JsonResponse
    {

        $is_successful = $this->isStatusCodeSuccessful($status);

        $response_data = [
            'status' => $is_successful,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response_data[$is_successful ? 'data' : 'error'] = $data;
        }

        return Response::json($response_data, $status);
    }

    /**
     * Determine if a  HTTP status code indicates success
     *
     * @param int $status
     *
     * @return bool
     */
    public function isStatusCodeSuccessful(int $status): bool
    {
        return $status >= 200 && $status < 300;
    }

    public function authResponse($message, $status): JsonResponse
    {
        return $this->jsonResponse($message, $status);
    }

    public function tooManyRequests($message, $status = 429): JsonResponse
    {
        return $this->jsonResponse($message, $status);
    }
}
