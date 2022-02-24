<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Actions\Auth\UserLogin;
use App\Http\Controllers\API\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;

class LoginController extends BaseController
{
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
