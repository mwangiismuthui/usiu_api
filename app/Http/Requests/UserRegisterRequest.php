<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'full_name' =>  'required|min:3',
            'student_id' =>  'required|min:3|unique:users',
            'email' =>      'required|email|unique:users',
            // 'username' =>     'required|min:3',
            // 'phone' =>      'required',
            // 'DOB' =>      'required',
            // 'gender' =>      'required',
            // 'about' =>      'required_if:user_type,==,coach',
            'password' =>   'required',
          ];
    }
}
