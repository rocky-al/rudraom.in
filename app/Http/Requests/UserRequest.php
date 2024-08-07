<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserRequest extends FormRequest
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
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'mobile' => "required||integer|unique:users,mobile," . $id,
            'email' => "sometimes|max:50|required|unique:users,email," . $id,
            'state_id' => 'required|max:50',
            'city_id' => 'required|max:50',
            'landmark' => 'required|max:50',
            'pincode' => 'required|min:4|max:10',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('Please provide first name'),
            'mobile.required' => __('Please provide mobile number'),
            'mobile.between' => __('The mobile number should be between 7 to 15 digits.'),
            'email.required' => __('Please provide Email'),
            'city_id.required' => 'Please provide a city.',
            'state_id.required' => 'Please provide state.',
            'pincode.required' => 'Please provide a pincode',
        ];
    }
}
