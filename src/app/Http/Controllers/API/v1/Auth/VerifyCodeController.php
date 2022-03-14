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
    public function __invoke(VerifyCodeRequest $request)
    {
        UserVerifyEmail::execute($request->validated());

        return $this->messageResponse(__('Email successfully verified.'), Response::HTTP_OK);
    }
}
