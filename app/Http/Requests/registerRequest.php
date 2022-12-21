<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        
             
        return [
            'firstname'=> ['required','string'],
            'lastname'=>['required','string'],
            'email'=>['required','exists:users,email'],
            'username'=>['required','exists:user,username'],
           'password'=>['required|string|min:6'],
       
                   
               ];
        
    }
}
