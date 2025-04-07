<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVideoRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                Rule::unique('videos', 'title')->ignore($this->route('video'))
            ],
            //Required video file if no video url link provided
            'video_file'   => ['required_without:video_url', 'file', 'mimes:mp4,avi,mpeg,mkv', 'max:102400'],
            //Required video url if no video file provided
            'video_url'    => ['required_without:video_file', 'url'],
            'description' => ['nullable', 'string'],
            'category_ids' => ['required', 'array'],
            'category_ids.*' => ['exists:categories,id'],
        ];
    }
}
