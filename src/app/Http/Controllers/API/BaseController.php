<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\JsonResponser;

/**
 * @OA\Info(
 *    title="Laravel docker base",
 *    version="1.0.0",
 * )
 */
class BaseController extends Controller
{
    use JsonResponser;
}
