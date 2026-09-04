<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
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
            'title' =>[
                'required',
                'string',
                'max:255',
            ],
            'author' =>[
                'required',
                'string',
                'max:255',
            ],
            'isbn' =>[
                'required',
                'digits:13',
                Rule::unique('books', 'isbn'),
            ],
            'published_date' =>[
                'required',
                'date',
            ],
            'description' =>[
                'nullable',
                'string',
            ],
            'image_url' =>[
                'nullable',
                'url',
            ],
            'genres' => [
                'required',
                'array',
                'min:1',
            ],
            'genres.*' =>[
                'integer',
                'exists:genres,id',
                'distinct',
            ],
        ];
    }

    public function messages(): array
{
    return [
        'title.required' => 'タイトルを入力してください。',
        'title.string' => 'タイトルは文字列で指定してください。',
        'title.max' => 'タイトルは255文字以内で指定してください。',

        'author.required' => '著者名を入力してください。',
        'author.string' => '著者名は文字列で指定してください。',
        'author.max' => '著者名は255文字以内で指定してください。',

        'isbn.required' => 'ISBNを入力してください。',
        'isbn.digits' => 'ISBNは13桁で指定してください。',
        'isbn.unique' => 'このISBNは既に登録されています。',

        'published_date.required' => '出版日を入力してください。',
        'published_date.date' => '正しい日付を指定してください。',

        'description.string' => '説明は文字列で指定してください。',

        'image_url.url' => '画像URLは正しいURL形式で指定してください。',

        'genres.required' => 'ジャンルを1つ以上指定してください。',
        'genres.array' => 'ジャンルは配列で指定してください。',
        'genres.min' => 'ジャンルを1つ以上指定してください。',
        'genres.*.integer' => 'ジャンルIDは整数で指定してください。',
        'genres.*.exists' => '指定したジャンルが存在しません。',
        'genres.*.distinct' => '同じジャンルを重複して指定することはできません。',
    ];
}
}
