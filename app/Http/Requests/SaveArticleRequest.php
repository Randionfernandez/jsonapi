<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveArticleRequest extends FormRequest
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
     *
     * @todo no permitir espacios en blanco
     */
    public function rules(): array
    {
        return [
//            'data' => ['required'],
            'data.attributes.title' => ['required', 'min:4'],
            'data.attributes.slug' => [
                'required',
                new Slug,
//                'regex:/^[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*$/',
                Rule::unique('articles', 'slug')->ignore($this->route('article'))],
            'data.attributes.content' => ['required'],
        ];
    }
}
