<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TechnicianRequest extends FormRequest
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
            'first_name' => 'required|min:2|max:25',
            'last_name' => 'required|min:2|max:25',
            'mobile' => "required||integer|unique:users,mobile," . $id,
            'alternate_mobile' => "required||integer|unique:users,alternate_mobile," . $id,
            'email' => "sometimes|required|unique:users,email," . $id,
            'state_id' => 'required|integer',
            'city_id' => 'required|integer',
            'landmark' => 'required',
            'pencard_number' => 'required',
            'account_type' => 'required',
            'account_holder_name' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required|numeric',
            'ifsc' => 'required',
            'service_charge' => 'required|integer',
            'pincode' => 'required|digits_between: 5,10',
        ];

    }
}
