<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Actions\Auth\UserRegister;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Response;

class RegisterController extends BaseController
{
    /**
    * @OA\Post(
    * path="/register",
    * operationId="Register User",
    * tags={"User Authentication"},
    * summary="Register User",
    * description="Register user here",
    *     @OA\RequestBody(
    *         @OA\JsonContent(
    *            @OA\Schema(
    *               type="object",
    *               required={"name","email", "password"},
    *               @OA\Property(property="name", type="text"),
    *               @OA\Property(property="email", type="text"),
    *               @OA\Property(property="password", type="password")
    *            ),
    *        )
    *    ),
    *      @OA\Response(
    *          response=201,
    *          description="Register Successfully",
    *          @OA\JsonContent(
    *             @OA\Schema(
    *                  type="object",
    *                  @OA\Property(property="message", type="text")
    *            ),
    *          )
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
    public function __invoke(RegisterRequest $request)
    {
        UserRegister::execute($request->all());

        return $this->messageResponse(__('User created successfuly. Please check your email for verification code.'), Response::HTTP_CREATED);
    }
}
