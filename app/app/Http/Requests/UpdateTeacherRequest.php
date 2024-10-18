<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\existInSection;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required'],
            'username' => [ 'required', 'unique:users,username,'. request()->id],
            'first_name' => [ 'required'],
            'last_name' => [ 'required', 
            function($kay, $value, $fail) {
                $exist = User::whereNotIn('id', [request()->id])->where('first_name', [request()->first_name])->where('last_name', [request()->last_name])->exists();

                if($exist) {
                    $fail('User Already Exists');
                }
            }],
            'sections' => [ 'required' ],
            'status'  => [ 'required' ],
        ];
    }
}
