<?php

namespace App\Http\Requests\Admin;

use App\Enums\SettingKey;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return collect(SettingKey::cases())
            ->mapWithKeys(fn (SettingKey $key) => [$key->value => $key->field()->rules()])
            ->all();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return collect(SettingKey::cases())
            ->mapWithKeys(fn (SettingKey $key) => [$key->value => strtolower($key->label())])
            ->all();
    }
}
