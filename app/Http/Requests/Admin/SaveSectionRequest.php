<?php

namespace App\Http\Requests\Admin;

use App\Enums\SectionType;
use App\Http\Requests\Admin\Concerns\HandlesContentFields;
use App\Models\Section;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SaveSectionRequest extends FormRequest
{
    use HandlesContentFields;

    /**
     * Element ids used by the page layout, which a section anchor may not take.
     *
     * @var list<string>
     */
    public const array RESERVED_ANCHORS = ['top', 'main', 'site-nav', 'contact-form'];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Derive the anchor from the title when it is left empty, and normalise it to a slug.
     */
    protected function prepareForValidation(): void
    {
        $anchor = trim((string) $this->input('anchor'));

        $this->merge([
            'anchor' => Str::slug($anchor === '' ? (string) $this->input('title') : $anchor),
            'is_visible' => $this->boolean('is_visible'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $section = $this->section();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'anchor' => [
                'required',
                'string',
                'max:64',
                Rule::notIn(self::RESERVED_ANCHORS),
                Rule::unique('sections', 'anchor')->ignore($section),
            ],
            'nav_label' => ['nullable', 'string', 'max:64'],
            'is_visible' => ['boolean'],
        ];

        if ($section === null) {
            $rules['type'] = ['required', Rule::enum(SectionType::class), $this->singletonRule()];
        }

        return [...$rules, ...$this->contentRules($this->sectionType()?->fields() ?? [])];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'titel',
            'anchor' => 'ankernaam',
            'nav_label' => 'naam in het menu',
            ...$this->contentAttributes($this->sectionType()?->fields() ?? []),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'anchor.not_in' => 'Deze ankernaam is gereserveerd. Kies een andere.',
        ];
    }

    /**
     * Get the type of the section being created or updated.
     */
    public function sectionType(): ?SectionType
    {
        return $this->section()?->type ?? SectionType::tryFrom((string) $this->input('type'));
    }

    /**
     * Get the validated attributes to store on the section.
     *
     * @return array{title: string, anchor: string, nav_label: ?string, is_visible: bool, content: array<string, string|int|bool|null>}
     */
    public function sectionAttributes(): array
    {
        return [
            'title' => $this->validated('title'),
            'anchor' => $this->validated('anchor'),
            'nav_label' => $this->validated('nav_label'),
            'is_visible' => (bool) $this->validated('is_visible'),
            'content' => $this->contentValues($this->sectionType()?->fields() ?? []),
        ];
    }

    /**
     * Get the section being updated, if any.
     */
    private function section(): ?Section
    {
        $section = $this->route('section');

        return $section instanceof Section ? $section : null;
    }

    /**
     * Reject a second section of a type that may only appear once.
     */
    private function singletonRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $type = SectionType::tryFrom((string) $value);

            if ($type?->isSingleton() && Section::query()->where('type', $type)->exists()) {
                $fail('Er is al een sectie van dit type. Dit type kan maar één keer op de pagina staan.');
            }
        };
    }
}
