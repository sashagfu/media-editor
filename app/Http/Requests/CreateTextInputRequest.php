<?php

namespace App\Http\Requests;

use App\Services\Studio\Entities\Font;
use App\Services\Studio\Repositories\FontRepository;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read string $content
 * @property-read string $font
 * @property-read string $font_type
 * @property-read string $color
 * @property-read string $background
 * @property-read float $size
 */

class CreateTextInputRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->json();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required',
            'font' => 'required|in:' . implode(',', (new FontRepository())->available()),
            'font_type' => 'sometimes|in:' . implode(',', Font::make($this->font)->types),
            'size'  => 'sometimes|numeric|min:1',
            'align' => 'sometimes|in:left,right,center',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->color && !$this->isColor($this->color)) {
                $validator->errors()->add('color', trans('validation.regex', ['attribute' => 'color']));
            }
            if ($this->background && !$this->isColor($this->background)) {
                $validator->errors()->add('background', trans('validation.regex', ['attribute' => 'background']));
            }
        });
    }

    /**
     *  Check color string (it's a hex string or a json rgb array)
     * @param $value
     * @return bool
     */
    private function isColor($value)
    {
        if (ctype_xdigit($value) && in_array(strlen($value), [6, 8])) {
            return true;
        }

        /** @var array|null $rgb */
        $rgb = json_decode($value);

        if (!empty($rgb) && count($rgb) >= 3) {
            return true;
        }

        return false;
    }
}
