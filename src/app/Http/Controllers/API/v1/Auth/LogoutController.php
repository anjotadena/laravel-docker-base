<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\API\BaseController as APIBaseController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LogoutController extends APIBaseController
{
    public function __invoke()
    {
        $user = request()->user();

        $user->tokens()->where('id', auth()->id())->delete();

        return $this->successResponse(null, Response::HTTP_NO_CONTENT);
    }
}
