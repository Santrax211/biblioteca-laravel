<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->isRole('bibliotecario') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>['required','string','max:255'],
            'isbn'=>['required','string','max:20','unique:books,isbn'],
            'year'=>['nullable','digits:4'],
            'category_id'=>['required','exists:categories,id'],
            'stock_total'=>['required','integer','min:1'],
            'authors'=>['required','array','min:1'],
            'authors.*'=>['exists:authors,id'],
        ];
    }
}
