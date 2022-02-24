<?php

namespace App\Exceptions;

use App\Traits\JsonResponser;
use Exception;
use Illuminate\Http\Response;

class InvalidCredentialsException extends Exception
{
    use JsonResponser;

    public function __construct($message = 'Invalid credentials')
    {
        parent::__construct(__($message));   
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return $this->messageResponse($this->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
