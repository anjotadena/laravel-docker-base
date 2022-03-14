<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponser;

/**
 * @OA\Info(
 *      version="1.0.3",
 *      title="Laravel Base API Documentation",
 *      description="Coll RESTFul API for laravel base",
 *      @OA\Contact(
 *          email="tadena.anjo@gmail.com"
 *      ),
 *      @OA\License(
 *          name="MIT",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Larabel Base"
 * )
 * @OA\Tag(
 *     name="Laravel Base",
 *     description="API Endpoints of Laravel Base"
 * )
 */
class BaseController extends Controller
{
    use JsonResponser;
}
