<?php

namespace Database\Factories;

use App\Enums\SectionType;
use App\Models\Section;
use App\Support\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => SectionType::Text,
            'title' => fake()->sentence(4),
            'anchor' => fake()->unique()->slug(2),
            'nav_label' => null,
            'is_visible' => true,
            'content' => ['body' => fake()->paragraph(), 'background' => 'wit'],
        ];
    }

    /**
     * Use the given type, with the default values of its fields as content.
     */
    public function ofType(SectionType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'content' => collect($type->fields())
                ->mapWithKeys(fn (FormField $field) => [$field->name => $field->default])
                ->all(),
        ]);
    }

    /**
     * Indicate that the section is hidden on the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }
}
