<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StaffRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $id = $request->get('id');
        return [
            'role' => 'required',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'mobile' => "required||integer|unique:users,mobile," . $id,
            'email' => "sometimes|max:50|required|unique:users,email," . $id,
        ];
    }
}
