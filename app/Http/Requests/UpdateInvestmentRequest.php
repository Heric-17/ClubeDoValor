<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'investment_date' => ['required', 'date', 'before_or_equal:today'],
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
