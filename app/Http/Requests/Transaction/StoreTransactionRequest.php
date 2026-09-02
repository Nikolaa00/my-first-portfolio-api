<?php

namespace App\Http\Requests\Transaction;

use App\Enums\Currency;
use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
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
            'asset_id' => ['required', 'integer', 'exists:assets,id'],
            'type' => ['required', Rule::enum(TransactionType::class)],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'price' => ['required', 'numeric', 'gt:0'],
            'price_currency' => ['required', Rule::enum(Currency::class)],
            'executed_at' => ['required', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'asset_id.required' => 'The asset is required.',
            'asset_id.integer' => 'The asset must be a valid identifier.',
            'asset_id.exists' => 'The selected asset does not exist.',
            'type.required' => 'The transaction type is required.',
            'type.enum' => 'The transaction type must be buy or sell.',
            'quantity.required' => 'The quantity is required.',
            'quantity.numeric' => 'The quantity must be a number.',
            'quantity.gt' => 'The quantity must be greater than zero.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.gt' => 'The price must be greater than zero.',
            'price_currency.required' => 'The price currency is required.',
            'price_currency.enum' => 'The selected price currency is invalid.',
            'executed_at.required' => 'The execution date is required.',
            'executed_at.date' => 'The execution date must be a valid date.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'asset_id' => 'asset',
            'type' => 'transaction type',
            'quantity' => 'quantity',
            'price' => 'price',
            'price_currency' => 'price currency',
            'executed_at' => 'execution date',
        ];
    }
}
