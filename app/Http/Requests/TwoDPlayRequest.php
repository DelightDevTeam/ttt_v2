<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwoDPlayRequest extends FormRequest
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
            'selected_digits' => 'required|string',
            'totalAmount' => 'required|numeric|min:1',
            'amounts' => 'required|array',
            'amounts.*.num' => 'required|integer',
            'amounts.*.amount' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'totalAmount.required' => 'Total amount is required.',
            'totalAmount.numeric' => 'Total amount must be a numeric value.',
            'totalAmount.min' => 'Total amount must be at least 1.',
            'amounts.required' => 'Amounts are required.',
            'amounts.array' => 'Amounts must be an array.',
            'amounts.*.num.required' => 'Each bet must have a number.',
            'amounts.*.num.integer' => 'Each bet number must be an integer.',
            'amounts.*.amount.required' => 'Each bet must have an amount.',
            'amounts.*.amount.integer' => 'Each bet amount must be an integer.',
            'amounts.*.amount.min' => 'Each bet amount must be at least 1.',
        ];
    }
}
