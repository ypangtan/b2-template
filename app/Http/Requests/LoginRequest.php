<?php

namespace App\Http\Requests;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Fortify;

class LoginRequest extends FortifyLoginRequest
{
    public function authorize()
    {
        return true;
    }

    // Turn off Fortify validation rules
    public function rules()
    {
        return [];
    }
}