<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $itemId = $this->route('item');
        
        return [
            'name' => 'required|string|min:3|max:180',
            'code' => 'nullable|string|max:50|unique:items,code,' . $itemId,
            'category' => 'nullable|string|max:100|in:Eletrônicos,Alimentos,Vestuário,Móveis,Livros,Brinquedos,Ferramentas,Outros',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'quantity' => 'required|integer|min:0|max:2147483647',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string' => 'O campo nome deve ser uma string.',
            'name.min' => 'O campo nome deve ser no mínimo 3 caracteres.',
            'name.max' => 'O campo nome não pode exceder 180 caracteres.',

            'code.string' => 'O campo código deve ser uma string.',
            'code.max' => 'O campo código não pode exceder 50 caracteres.',
            'code.unique' => 'Este código já está em uso.',

            'category.string' => 'O campo categoria deve ser uma string.',
            'category.max' => 'O campo categoria não pode exceder 100 caracteres.',
            'category.in' => 'A categoria selecionada é inválida.',

            'description.string' => 'O campo descrição deve ser uma string.',
            'description.max' => 'O campo descrição não pode exceder 5000 caracteres.',

            'price.required' => 'O campo preço é obrigatório.',
            'price.numeric' => 'O campo preço deve ser um número.',
            'price.min' => 'O campo preço deve ser no mínimo 0.',
            'price.max' => 'O campo preço não pode exceder 999999.99.',

            'quantity.required' => 'O campo quantidade é obrigatório.',
            'quantity.integer' => 'O campo quantidade deve ser um inteiro.',
            'quantity.min' => 'O campo quantidade deve ser no mínimo 0.',
            'quantity.max' => 'O campo quantidade não pode exceder 2147483647.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
