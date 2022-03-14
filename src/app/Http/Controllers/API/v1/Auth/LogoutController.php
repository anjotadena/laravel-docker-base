<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\API\BaseController as APIBaseController;
use Illuminate\Http\Response;

/**
 * @OA\Post(
 * path="/logout",
 * operationId="Logout User",
 * tags={"User Authentication"},
 * summary="Logout User",
 * description="Logout user here",
 *     @OA\RequestBody(
 *         @OA\JsonContent()
 *     ),
 *      @OA\Response(
 *          response=201,
 *          description="Register Successfully",
 *         @OA\JsonContent(
 *             @OA\Schema(
 *                  type="object",
 *                  @OA\Property(property="message", type="text")
 *            ),
 *          )
 *       ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized user",
 *          @OA\JsonContent(
 *              @OA\Schema(
 *                  type="object",
 *                  @OA\Property(property="message", type="text")
 *              ),
 *          )
 *       ),
 * )
 */
class LogoutController extends APIBaseController
{
    public function __invoke()
    {
        $user = request()->user();

        $user->tokens()->where('id', auth()->id())->delete();

        return $this->successResponse(null, Response::HTTP_NO_CONTENT);
    }
}
