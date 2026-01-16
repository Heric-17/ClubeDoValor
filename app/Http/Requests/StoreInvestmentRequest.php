<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvestmentRequest extends FormRequest
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
            'asset_id' => ['required', 'exists:assets,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'investment_date' => ['required', 'date', 'before_or_equal:today'],
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
