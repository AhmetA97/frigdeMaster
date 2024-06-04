<?php

namespace App\Http\Requests;

use App\Rules\Max24Days;
use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'volume' => 'required|numeric|min:4',
            'temperature' => 'required|numeric|min:1|max:99',
            'date_start' => ['required', 'date_format:Y-m-d', 'before:date_end', 'after_or_equal:today'],
            'date_end' => ['required', 'date_format:Y-m-d', new Max24Days($this->request->get('date_start'))],
        ];
    }
}
