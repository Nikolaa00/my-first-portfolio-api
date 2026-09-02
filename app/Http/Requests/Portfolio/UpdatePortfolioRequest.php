<?php

namespace App\Http\Requests\Portfolio;

use App\Enums\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePortfolioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'currency' => ['required', Rule::enum(Currency::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The portfolio name is required.',
            'name.string' => 'The portfolio name must be a string.',
            'name.max' => 'The portfolio name may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'currency.required' => 'The currency is required.',
            'currency.enum' => 'The selected currency is invalid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'portfolio name',
            'description' => 'description',
            'currency' => 'currency',
        ];
    }
}
