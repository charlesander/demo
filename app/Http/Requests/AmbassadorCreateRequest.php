<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmbassadorCreateRequest extends FormRequest
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
            'xxxxxxxxxxxx' => 'required:email',
            'xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx' => 'required:string',
            'xxxxxxxxxxxx' => 'required:string',
            'xxxxxxxxxxxx' => 'date',
            'xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx' => 'boolean',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'exists:\App\Models\Country,id',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'string',
            'xxxxxxxxxxxx.xxxxxxxxxxxx' => 'exists:\App\Models\Country,id',
            'xxxxxxxxxxxx' => 'required|exists:\App\Models\AmbassadorLegalStatus,id',
            'xxxxxxxxxxxx' => 'required|boolean',
            'xxxxxxxxxxxx' => 'required|boolean',
            'xxxxxxxxxxxx' => 'numeric',
            'xxxxxxxxxxxx' => 'numeric',
            'xxxxxxxxxxxx' => 'numeric',
            'xxxxxxxxxxxx' => 'numeric'
        ];
    }
}
