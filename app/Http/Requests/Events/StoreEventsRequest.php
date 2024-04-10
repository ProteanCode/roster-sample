<?php

namespace App\Http\Requests\Events;

use App\Classes\Enums\ParserSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_type' => Rule::enum(ParserSource::class),
            'source_data' => 'required|file',
        ];
    }
}
