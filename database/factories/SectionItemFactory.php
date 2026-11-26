<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\SectionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SectionItem>
 */
class SectionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'group' => 'kaart',
            'title' => fake()->sentence(3),
            'content' => [],
        ];
    }
}
