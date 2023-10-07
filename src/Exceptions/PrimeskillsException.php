<?php

namespace Primeskills\ApiCommon\Exceptions;

use RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class PrimeskillsException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * @var int $statusCode
     */
    private $statusCode;

    /**
     * @var array $headers
     */
    private $headers;

    /**
     * @var array|mixed $data
     */
    private $data;

    /**
     * @var string $resources
     */
    private $resources;

    /**
     * @param int $statusCode
     * @param string|null $message
     * @param string|null $resources
     * @param array $data
     * @param Throwable|null $previous
     * @param array $headers
     * @param int|null $code
     */
    public function __construct(int $statusCode, ?string $message = '', array $data = [], ?string $resources = '', \Throwable $previous = null, array $headers = [], ?int $code = 0)
    {
        if ($message === null) {
            trigger_deprecation('symfony/http-kernel', '5.3', 'Passing null as $message to "%s()" is deprecated, pass an empty string instead.', __METHOD__);

            $message = '';
        }
        if (null === $code) {
            trigger_deprecation('symfony/http-kernel', '5.3', 'Passing null as $code to "%s()" is deprecated, pass 0 instead.', __METHOD__);

            $code = 0;
        }

        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->data = $data;
        $this->resources = $resources;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array|mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getResources(): string
    {
        return $this->resources;
    }

}
