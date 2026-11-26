<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\HandlesContentFields;
use App\Models\Section;
use App\Models\SectionItem;
use App\Support\FormField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSectionItemRequest extends FormRequest
{
    use HandlesContentFields;

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
        $title = collect($this->fields())->firstWhere('name', 'title');

        $rules = [
            'title' => $title?->rules() ?? ['required', 'string', 'max:255'],
            ...$this->contentRules($this->contentFields()),
        ];

        if ($this->item() === null) {
            $rules['group'] = ['required', 'string', Rule::in(array_keys($this->section()->type->itemGroups()))];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        $title = collect($this->fields())->firstWhere('name', 'title');

        return [
            'title' => strtolower($title?->label ?? 'titel'),
            ...$this->contentAttributes($this->contentFields()),
        ];
    }

    /**
     * Get the group of the item being created or updated.
     */
    public function group(): string
    {
        return $this->item()?->group ?? (string) $this->input('group');
    }

    /**
     * Get the validated attributes to store on the item.
     *
     * @return array{title: string, content: array<string, string|int|bool|null>}
     */
    public function itemAttributes(): array
    {
        return [
            'title' => $this->validated('title'),
            'content' => $this->contentValues($this->contentFields()),
        ];
    }

    /**
     * Get all fields of the item's group.
     *
     * @return list<FormField>
     */
    private function fields(): array
    {
        return $this->section()->type->itemFields($this->group());
    }

    /**
     * Get the fields that are stored in the item's content.
     *
     * @return list<FormField>
     */
    private function contentFields(): array
    {
        return array_values(array_filter($this->fields(), fn (FormField $field) => $field->name !== 'title'));
    }

    /**
     * Get the section the item belongs to.
     */
    private function section(): Section
    {
        return $this->route('section');
    }

    /**
     * Get the item being updated, if any.
     */
    private function item(): ?SectionItem
    {
        $item = $this->route('item');

        return $item instanceof SectionItem ? $item : null;
    }
}
