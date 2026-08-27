<?php

namespace App\Http\Requests\Admin;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return ['status' => ['required', Rule::enum(ShipmentStatus::class)], 'carrier' => ['nullable', 'string', 'max:255'], 'tracking_code' => ['nullable', 'string', 'max:255']];
    }
}
