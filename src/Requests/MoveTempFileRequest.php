<?php

namespace Elgndy\FileUploader\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveTempFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'model' => 'required|string',
            'id' => 'required|integer',
            'tempPath' => 'required|string',
        ];
    }
}
