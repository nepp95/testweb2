<?php

namespace App\Http\Requests;

use App\Enums\SectionType;
use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactMessageRequest extends FormRequest
{
    /**
     * The visible contact section, resolved once per request.
     */
    private Section|false|null $contactSection = false;

    /**
     * The form can only be used while the contact section is on the page.
     */
    public function authorize(): bool
    {
        return $this->contactSection() !== null;
    }

    /**
     * Respond as if the form does not exist when the contact section is hidden or removed.
     */
    protected function failedAuthorization(): void
    {
        abort(404);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $subjects = $this->subjects();

        return [
            'onderwerp' => $subjects === [] ? ['prohibited'] : ['required', 'string', Rule::in($subjects)],
            'naam' => ['required', 'string', 'max:100'],
            'contact' => ['required', 'string', 'min:6', 'max:255'],
            'bericht' => ['nullable', 'string', 'max:5000'],
            'privacy' => ['accepted'],
            'website' => ['nullable', 'string', 'max:255'],
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
            'onderwerp.*' => 'Kies een onderwerp uit de lijst.',
            'naam.required' => 'Vul een naam in, zodat we weten hoe we je kunnen aanspreken.',
            'naam.max' => 'Je naam mag niet langer zijn dan 100 tekens.',
            'contact.required' => 'Vul een telefoonnummer of e-mailadres in.',
            'contact.min' => 'Vul een telefoonnummer of e-mailadres in.',
            'contact.max' => 'Dit is te lang voor een telefoonnummer of e-mailadres.',
            'bericht.max' => 'Je bericht mag niet langer zijn dan 5000 tekens.',
            'privacy.accepted' => 'Vink dit aan, anders mogen we geen contact met je opnemen.',
        ];
    }

    /**
     * Determine whether the hidden honeypot field was filled in, which only bots do.
     */
    public function isLikelySpam(): bool
    {
        return $this->filled('website');
    }

    /**
     * Send the visitor back to the form when validation fails.
     */
    protected function getRedirectUrl(): string
    {
        return route('home').'#contact-form';
    }

    /**
     * Get the subjects a visitor can choose from.
     *
     * @return list<string>
     */
    private function subjects(): array
    {
        return $this->contactSection()
            ?->itemsInGroup(SectionType::CONTACT_SUBJECT_GROUP)
            ->pluck('title')
            ->all() ?? [];
    }

    /**
     * Get the visible contact section with its items.
     */
    private function contactSection(): ?Section
    {
        if ($this->contactSection === false) {
            $this->contactSection = Section::query()
                ->visible()
                ->where('type', SectionType::Contact)
                ->with('items')
                ->first();
        }

        return $this->contactSection;
    }
}
