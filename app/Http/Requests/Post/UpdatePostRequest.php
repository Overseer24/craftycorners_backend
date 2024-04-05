<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserBelongsToCommunity;

class UpdatePostRequest extends FormRequest
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
            'title' => 'nullable|string',
            'content' => 'nullable|string',
//            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:61440',
//            'video' => 'nullable|mimes:mp4,mov,ogg,qt|max:512000',
//            'link' => 'nullable|url',
            'community_id' => ['nullable', 'exists:communities,id', new UserBelongsToCommunity($this->community_id)],
            'likes' => 'nullable|integer',
            'shares' => 'nullable|integer',
            'comments' => 'nullable|integer',
            'post_type' => 'nullable|string',
            'notifiable' => 'nullable|string|in:true,false',
        ];
    }
}
