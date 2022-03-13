<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Actions\Auth\UserRegister;
use App\Actions\Auth\UserVerifyEmail;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use App\Http\Resources\Auth\RegisterResource;
use Illuminate\Http\Response;

class VerifyCodeController extends BaseController
{
    /**
    * @OA\Post(
    * path="/api/v1/register",
    * operationId="Register",
    * tags={"Register"},
    * summary="User Register",
    * description="User Register here",
    *     @OA\RequestBody(
    *         @OA\JsonContent(),
    *         @OA\MediaType(
    *            mediaType="multipart/form-data",
    *            @OA\Schema(
    *               type="object",
    *               required={"name","email", "password", "password_confirmation"},
    *               @OA\Property(property="name", type="text"),
    *               @OA\Property(property="email", type="text"),
    *               @OA\Property(property="password", type="password"),
    *               @OA\Property(property="password_confirmation", type="password")
    *            ),
    *        ),
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Register Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=200,
    *          description="Register Successfully",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(
    *          response=422,
    *          description="Unprocessable Entity",
    *          @OA\JsonContent()
    *       ),
    *      @OA\Response(response=400, description="Bad request"),
    *      @OA\Response(response=404, description="Resource Not Found"),
    * )
    */
    public function __invoke(VerifyCodeRequest $request)
    {
        UserVerifyEmail::execute($request->validated());

        return $this->messageResponse(__('Email successfully verified.'), Response::HTTP_OK);
    }
}
