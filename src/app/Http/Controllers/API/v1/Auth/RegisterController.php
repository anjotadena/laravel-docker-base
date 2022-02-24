<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Actions\Auth\UserRegister;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\RegisterResource;
use Illuminate\Http\Response;

class RegisterController extends BaseController
{
    public function __invoke(RegisterRequest $request)
    {
        return $this->successResponse(
            new RegisterResource(
                UserRegister::execute($request->all())
            ), Response::HTTP_CREATED);
    }
}
