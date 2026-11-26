<?php

namespace App\Http\Requests\Admin\Concerns;

use App\Support\FormField;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates and collects type-specific fields that are submitted as content[...] and stored in a JSON column.
 *
 * @mixin FormRequest
 */
trait HandlesContentFields
{
    /**
     * Build the validation rules for the given content fields.
     *
     * @param  list<FormField>  $fields
     * @return array<string, array<int, mixed>>
     */
    protected function contentRules(array $fields): array
    {
        return collect($fields)
            ->mapWithKeys(fn (FormField $field) => ['content.'.$field->name => $field->rules()])
            ->all();
    }

    /**
     * Collect the validated values of the given content fields.
     *
     * @param  list<FormField>  $fields
     * @return array<string, string|int|bool|null>
     */
    protected function contentValues(array $fields): array
    {
        $content = (array) $this->validated('content', []);

        return collect($fields)
            ->mapWithKeys(fn (FormField $field) => [$field->name => $field->normalise($content[$field->name] ?? null)])
            ->all();
    }

    /**
     * Get the attribute names used in validation messages.
     *
     * @param  list<FormField>  $fields
     * @return array<string, string>
     */
    protected function contentAttributes(array $fields): array
    {
        return collect($fields)
            ->mapWithKeys(fn (FormField $field) => ['content.'.$field->name => strtolower($field->label)])
            ->all();
    }
}
