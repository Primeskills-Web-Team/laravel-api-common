<?php
/*
 * Copyright (c) 2023.
 * Created At: 1/20/23, 9:52 AM
 * Created By: Muhammad Suryono
 */

namespace Primeskills\ApiCommon\Response;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Primeskills\ApiCommon\Exceptions\PrimeskillsException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Primeskills\ApiCommon\Traits\PrimeskillsLog;


/**
 *
 */
class ResponseBuilder
{
    use PrimeskillsLog;
    /**
     * @var int
     */
    private $code;
    /**
     * @var string
     */
    private $message;
    /**
     * @var null
     */
    private $data;
    /**
     * @var int
     */
    private $status;
    /**
     * @var Exception
     */
    private $exception;
    /**
     * @var array
     */
    private $errors;

    /**
     * @var bool
     */
    private $success;

    /**
     * @var string
     */
    private $responseCode;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var string
     */
    private $resources;

    /**
     *
     */
    public function __construct()
    {
        $this->responseCode = ResponseConstant::SUCCESS_CODE;
        $this->message = ResponseConstant::SUCCESS_DESCRIPTION;
        $this->data = null;
        $this->success = true;
        $this->resources = env("APP_NAME") ?? "-";
        $this->code = 200;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): ResponseBuilder
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): ResponseBuilder
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data): ResponseBuilder
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function setStatus(bool $status): ResponseBuilder
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param Throwable $throwable
     * @return $this
     */
    public function instanceException(Throwable $throwable): ResponseBuilder
    {
        $this->exception = Response::builder()->checkTypeException($throwable);
        $this->exceptionHandle();
        return $this;
    }

    /**
     * @return array
     */
    public function build(): array
    {
        return [
            'response_code' => $this->responseCode,
            'success' => $this->responseCode === ResponseConstant::SUCCESS_CODE,
            "ref" => uniqid(rand(10*45, 999)),
            'message' => $this->message,
            'error_message' => $this->errorMessage,
            'resources' => $this->resources,
            "request" => [
                "headers" => collect(\request()->header())->transform(function ($item) {
                    return $item[0];
                }),
                "body" => \request()->input()
            ],
            "response" => [
                "headers" => collect(response()->headers)
            ],
            'data' => $this->data
        ];
    }

    /**
     * @return JsonResponse
     */
    public function buildJson(): JsonResponse
    {
        return response()->json($this->build(), $this->code);
    }

    /**
     * @return void
     */
    private function exceptionHandle(): void
    {
        $statusCode = 500;
        if (method_exists($this->exception, 'getStatusCode')) {
            $statusCode = $this->exception->getStatusCode();
        }

        $this->mapMessageDefaultStatusCode($statusCode);
        $this->setData($this->errors);
    }

    /**
     * @param int $statusCode
     * @return void
     */
    private function mapMessageDefaultStatusCode(int $statusCode): void
    {
        $this->write()->error("Error Code [$statusCode] " . $this->exception->getMessage());
        $this->setMessage(ResponseConstant::GENERAL_ERROR_DESCRIPTION);
        $this->setResponseCode(ResponseConstant::GENERAL_ERROR);
        $this->setCode($statusCode);

        if (method_exists($this->exception, 'getData')) {
            $this->errors = $this->exception->getData();
        }
        switch ($statusCode) {
            case 400:
                $this->setResponseCode(ResponseConstant::BAD_REQUEST_CODE);
                $this->setErrorMessage($this->exception->getMessage());
                break;
            case 401:
                $this->setResponseCode(ResponseConstant::UNAUTHORIZED_CODE);
                $this->setErrorMessage($this->exception->getMessage() == null ? ResponseConstant::UNAUTHORIZED_DESCRIPTION : $this->exception->getMessage());
                break;
            case 403:
                $this->setResponseCode(ResponseConstant::ROLE_FORBIDDEN_ACCESS_CODE);
                $this->setErrorMessage('Forbidden Access');
                break;
            case 404:
                $this->setResponseCode(ResponseConstant::NOT_FOUND_CODE);
                $this->setErrorMessage(ResponseConstant::NOT_FOUND_DESCRIPTION);
                break;
            case 405:
                $this->setResponseCode(ResponseConstant::METHOD_NOT_ALLOWED_CODE);
                $this->setErrorMessage(ResponseConstant::METHOD_NOT_ALLOWED_DESCRIPTION);
                break;
            case 419:
                $this->setResponseCode(ResponseConstant::TOKEN_MISMATCH_CODE);
                $this->setErrorMessage(ResponseConstant::TOKEN_MISMATCH_DESCRIPTION);
                break;
            case 422:
                $this->setResponseCode(ResponseConstant::VALIDATION_ERROR_CODE);
                $this->setErrorMessage($this->exception->getMessage());
                break;
            default:
                $this->setResponseCode("999");
                $this->setCode($statusCode);
                $message = strpos(strtolower($this->exception->getMessage()), 'sql') !== false && env('APP_ENV') == "production"
                    ? 'Whoops, looks like something went wrong on data access to database' : $this->exception->getMessage();
                $this->setErrorMessage(($statusCode == 500) ? $message : $this->exception->getMessage());
                break;
        }
    }

    /**
     * @param Exception $exception
     * @return ResponseBuilder
     */
    public function setException(Exception $exception) :ResponseBuilder
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @param Throwable $e
     * @return Throwable|AccessDeniedHttpException|HttpException|NotFoundHttpException
     */
    public function checkTypeException(Throwable $e)
    {
        if ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new AccessDeniedHttpException($e->getMessage(), $e);
        } elseif ($e instanceof TokenMismatchException) {
            $e = new HttpException(419, $e->getMessage(), $e);
        } elseif ($e instanceof SuspiciousOperationException) {
            $e = new NotFoundHttpException('Bad hostname provided.', $e);
        } elseif ($e instanceof AuthenticationException) {
            $e = new HttpException(401, $e->getMessage());
        } elseif ($e instanceof ValidationException) {
            $e = new PrimeskillsException($e->status, $e->getMessage(), [
                'errors' => $e->errors(),
            ]);
        } elseif ($e instanceof BadRequestException) {
            $e = new HttpException(400, $e->getMessage());
        }

        return $e;
    }

    /**
     * @return $this
     */
    public function version(): ResponseBuilder
    {
        $this->setData(['version' => env('APP_VERSION', '0.0.1')])
            ->setMessage('Success get service ' . env('APP_NAME'));
        return $this;
    }

    public function getResponseCode(): string
    {
        return $this->responseCode;
    }

    public function setResponseCode(string $responseCode): ResponseBuilder
    {
        $this->responseCode = $responseCode;
        return $this;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $errorMessage): ResponseBuilder
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getResources(): string
    {
        return $this->resources;
    }

    public function setResources(string $resources): ResponseBuilder
    {
        $this->resources = $resources;
        return $this;
    }

}
