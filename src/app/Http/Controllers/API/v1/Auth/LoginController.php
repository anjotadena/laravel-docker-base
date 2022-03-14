<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Actions\Auth\UserLogin;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;

/**
 * @OA\Post(
 *      path="/login",
 *      operationId="userLogin",
 *      tags={"User Authentication"},
 *      summary="Login registered user",
 *      description="Login registered user",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/LoginResource")
 *       ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request"
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden"
 *      )
 * )
 */
class LoginController extends BaseController
{
    /** */
    /**
     * POST Login user
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(LoginRequest $request)
    {
        return $this->successResponse(new LoginResource(
            UserLogin::execute([
                'email' => $request->email,
                'password' => $request->password
            ])
        ));
    }
}
