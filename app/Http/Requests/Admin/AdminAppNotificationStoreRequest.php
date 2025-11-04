<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\Auth\AuthEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class AdminAppNotificationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows(AuthEnum::SEND_APP_NOTIFICATION);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ];
    }
}
