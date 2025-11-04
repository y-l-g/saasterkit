<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Enums\Settings\ColorEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppearanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, \Illuminate\Validation\Rules\Enum|string>>
     */
    public function rules(): array
    {
        return [
            'primary_color' => ['nullable', Rule::enum(ColorEnum::class)],
            'secondary_color' => ['nullable', Rule::enum(ColorEnum::class)],
            'neutral_color' => ['nullable', Rule::enum(ColorEnum::class)],
        ];
    }
}
