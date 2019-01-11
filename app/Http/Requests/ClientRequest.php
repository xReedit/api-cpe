<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->input('id');
        $user = User::with('client')->find($id);
        $client_id = ($user)?$user->client->id:null;
        return [
            'name' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id),
            ],
            'company_name' => [
                'required',
            ],
            'company_number' => [
                'required',
                'size:11',
                Rule::unique('clients')->ignore($client_id),
            ],
            'soap_type_id' => [
                'required'
            ],
            'soap_username' => [
                'nullable',
                'required_if:soap_type_id,"02"'
            ],
            'soap_password' => [
                'nullable',
                'required_if:soap_type_id,"02"'
            ],
            'certificate' => [
                'nullable',
                'required_if:soap_type_id,"02"'
            ]
        ];
    }
}