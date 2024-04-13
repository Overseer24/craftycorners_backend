<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'message' => 'nullable|string',
//            'attachment' => 'nullable|file',
        ];
    }

//    public function withValidator($validator)
////    {
////        $validator->sometimes(['message', 'attachment'], 'required', function ($input) {
////            return empty($input->message) && !$input->hasFile('attachment');
////        });
////    }
}
