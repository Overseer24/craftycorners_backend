<?php

namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVideoRequest extends FormRequest
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
            'video_title' => 'required|string',
            'video_description' => 'required|string',
            'video_url' => 'required|string',
            'video_photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'creator' => 'required|string',
            'community_id' =>[
                'required_without:community_name',
                Rule::exists('communities', 'id')->where(function ($query) {
                    $query->where('id',$this->input('community_id'));
                }),
            ],
            'community_name'=>[
                'required_without:community_id',
                'string',
                'max:255',
                Rule::unique('communities', 'name')->where(function ($query) {
                    $query->where('id',$this->input('community_id'));
                }),
            ],
        ];
    }
}
