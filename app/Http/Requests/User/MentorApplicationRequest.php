<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class MentorApplicationRequest extends FormRequest
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
            'student_id' => 'required', 'integer',
            'program' => 'required', 'string',
            'community_id' => 'required', 'integer', 'exists:communities,id',
            'date_of_Assessment' => 'nullable', 'date',
            'specialization' => 'required', 'string',
        ];
    }
}
//'user_id' => $user->id,
//            'student_id' => 'required|string',
//            'program' => 'required|string',
//            'community' => 'required|integer|exists:communities,id',
//            'date_of_assessment' => 'nullable|date',
//            'specialization' => 'required|string'
