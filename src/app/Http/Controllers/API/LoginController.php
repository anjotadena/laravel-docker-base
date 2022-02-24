<?php

namespace App\Http\Controllers\API;

use App\Actions\Auth\UserLogin;
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
    public function store(LoginRequest $request)
    {
        return $this->successResponse(new LoginResource(
            UserLogin::execute([
                'email' => $request->email,
                'password' => $request->password
            ])
        ));
    }
}
