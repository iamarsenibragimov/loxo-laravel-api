<?php

namespace Loxo\LaravelApi\Exceptions;

use Exception;

class LoxoApiException extends Exception
{
    protected array $response;

    public function __construct(string $message, int $code = 0, array $response = [], ?Exception $previous = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the API response that caused the exception
     *
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }
}
