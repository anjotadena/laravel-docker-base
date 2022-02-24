<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait JsonResponser
{
    public function errorResponse($data, $code = null)
    {
        return response()->json($data, $code ?? Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function successResponse($data, $code = null)
    {
        return response()->json($data, $code ?? Response::HTTP_OK);
    }

    public function messageResponse($message, $code = null)
    {
        return response()->json(['message' => $message], $code ?? Response::HTTP_OK);
    }
}
