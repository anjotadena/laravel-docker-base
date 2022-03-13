<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class VerifyCodeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'code' => 'required'
        ];
    }
}
