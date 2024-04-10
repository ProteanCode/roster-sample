<?php

namespace App\Http\Requests\Events;

use App\Classes\Enums\EventType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', Rule::enum(EventType::class)],
            'from' => 'required_with:to|integer',
            'to' => 'required_with:from|integer|gt:from',
        ];
    }
}
