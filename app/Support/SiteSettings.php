<?php

namespace App\Support;

use App\Enums\SettingKey;
use App\Models\Setting;

/**
 * Reads the foundation's details, falling back to the placeholder defaults for settings that were never saved.
 */
class SiteSettings
{
    /**
     * @var array<string, string|null>|null
     */
    private ?array $values = null;

    /**
     * Get the value of a setting.
     */
    public function get(SettingKey $key): string
    {
        $this->values ??= Setting::query()->pluck('value', 'key')->all();

        return array_key_exists($key->value, $this->values)
            ? (string) $this->values[$key->value]
            : $key->default();
    }

    /**
     * Store the given values.
     *
     * @param  array<string, string|null>  $values  Setting key => value.
     */
    public function update(array $values): void
    {
        $rows = collect(SettingKey::cases())
            ->filter(fn (SettingKey $key) => array_key_exists($key->value, $values))
            ->map(fn (SettingKey $key) => ['key' => $key->value, 'value' => (string) $values[$key->value]])
            ->values()
            ->all();

        Setting::query()->upsert($rows, ['key'], ['value']);

        $this->values = null;
    }

    /**
     * Get the e-mail address shown on the website, if it is a valid address.
     */
    public function publicEmail(): ?string
    {
        return $this->validEmail($this->get(SettingKey::Email));
    }

    /**
     * Get the address that receives contact form messages.
     */
    public function formRecipient(): ?string
    {
        return $this->validEmail($this->get(SettingKey::FormRecipient)) ?? $this->publicEmail();
    }

    /**
     * Get the tel: link for the phone number, if it contains digits.
     */
    public function phoneHref(): ?string
    {
        $number = preg_replace('/[^0-9+]/', '', $this->get(SettingKey::Phone));

        return $number === '' ? null : 'tel:'.$number;
    }

    /**
     * Get the WhatsApp link for the WhatsApp number, if it contains digits.
     */
    public function whatsAppHref(): ?string
    {
        $number = preg_replace('/\D/', '', $this->get(SettingKey::WhatsApp));

        return $number === '' ? null : 'https://wa.me/'.$number;
    }

    /**
     * Return the address when it is a valid e-mail address.
     */
    private function validEmail(string $address): ?string
    {
        return filter_var($address, FILTER_VALIDATE_EMAIL) === false ? null : $address;
    }
}
