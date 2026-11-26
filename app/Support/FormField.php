<?php

namespace App\Support;

use App\Enums\FieldType;
use App\Enums\SectionType;
use Illuminate\Validation\Rule;

/**
 * Describes one editable field in the admin, used to render the input and to build its validation rules.
 */
final readonly class FormField
{
    /**
     * @param  array<string, string>  $options  Value => label, only used by select fields.
     */
    public function __construct(
        public string $name,
        public string $label,
        public FieldType $type = FieldType::Text,
        public bool $required = false,
        public ?string $help = null,
        public array $options = [],
        public string|bool|null $default = null,
    ) {}

    /**
     * Get the validation rules for this field.
     *
     * @return array<int, mixed>
     */
    public function rules(): array
    {
        if ($this->type === FieldType::Checkbox) {
            return ['boolean'];
        }

        $presence = $this->required ? 'required' : 'nullable';

        return match ($this->type) {
            FieldType::Textarea => [$presence, 'string', 'max:2000'],
            FieldType::Markdown => [$presence, 'string', 'max:20000'],
            FieldType::Email => [$presence, 'string', 'email', 'max:255'],
            FieldType::Select => [$presence, 'string', Rule::in(array_keys($this->options))],
            FieldType::SectionLink => [$presence, 'integer', Rule::exists('sections', 'id')],
            FieldType::ContactSubject => [$presence, 'integer', Rule::exists('section_items', 'id')->where(
                fn ($query) => $query->where('group', SectionType::CONTACT_SUBJECT_GROUP)
                    ->whereIn('section_id', fn ($sections) => $sections->select('id')->from('sections')->where('type', SectionType::Contact->value))
            )],
            default => [$presence, 'string', 'max:255'],
        };
    }

    /**
     * Normalise a validated value before it is stored.
     */
    public function normalise(mixed $value): string|int|bool|null
    {
        return match ($this->type) {
            FieldType::Checkbox => (bool) $value,
            FieldType::SectionLink, FieldType::ContactSubject => $value === null ? null : (int) $value,
            default => $value === null ? null : (string) $value,
        };
    }
}
