<?php

declare(strict_types=1);

namespace App\Http\Requests\Teams;

use App\Support\ReservedTeamName;
use Illuminate\Foundation\Http\FormRequest;

class TeamStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                new ReservedTeamName,
            ],
        ];
    }
}
