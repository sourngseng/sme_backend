<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

trait TraitLanguage
{
    /**
     * Supported application locales and metadata.
     *
     * @return array<string, array<string, string>>
     */
    public function getSupportedLanguages(): array
    {
        return [
            'en' => [
                'code' => 'en',
                'name' => 'English',
                'native' => 'English (US)',
                'flag' => '🇺🇸',
                'direction' => 'ltr',
            ],
            'km' => [
                'code' => 'km',
                'name' => 'Khmer',
                'native' => 'ភាសាខ្មែរ',
                'flag' => '🇰🇭',
                'direction' => 'ltr',
            ],
        ];
    }

    /**
     * Get the absolute path to a locale's JSON translation file.
     */
    public function getTranslationFilePath(string $locale): string
    {
        $cleanLocale = preg_replace('/[^a-zA-Z0-9_\-]/', '', strtolower($locale));
        return base_path("lang/{$cleanLocale}.json");
    }

    /**
     * Load all key-value translations from the specified locale's JSON file.
     *
     * @return array<string, string>
     */
    public function loadJsonTranslations(string $locale): array
    {
        $filePath = $this->getTranslationFilePath($locale);

        if (! File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Persist key-value translations into the specified locale's JSON file.
     *
     * @param array<string, string> $translations
     */
    public function saveJsonTranslations(string $locale, array $translations): bool
    {
        $langDir = base_path('lang');
        if (! File::isDirectory($langDir)) {
            File::makeDirectory($langDir, 0755, true);
        }

        $filePath = $this->getTranslationFilePath($locale);

        // Sort keys alphabetically for clean version control diffs
        ksort($translations, SORT_NATURAL | SORT_FLAG_CASE);

        $json = json_encode(
            $translations,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return File::put($filePath, $json) !== false;
    }

    /**
     * Translate a menu, form, or UI label using active locale and fallback.
     *
     * @param string $key
     * @param array<string, mixed>|string|null $replaceOrFallback If string, acts as fallback default. If array, acts as placeholder replacements.
     * @param array<string, mixed>|string|null $localeOrReplace If $replaceOrFallback is string, this can be $replace array or $locale string.
     * @param string|null $locale Explicit locale
     */
    public function transLabel(string $key, array|string|null $replaceOrFallback = [], array|string|null $localeOrReplace = null, ?string $locale = null): string
    {
        $fallback = null;
        $replace = [];
        $activeLocale = $locale;

        if (is_string($localeOrReplace)) {
            $activeLocale = $localeOrReplace;
        } elseif (is_array($localeOrReplace)) {
            $replace = $localeOrReplace;
        }

        if (is_string($replaceOrFallback)) {
            $fallback = $replaceOrFallback;
        } elseif (is_array($replaceOrFallback)) {
            $replace = $replaceOrFallback;
        }

        if ($locale) {
            $activeLocale = $locale;
        }

        $activeLocale = $activeLocale ?: App::getLocale() ?: 'en';
        $translations = $this->loadJsonTranslations($activeLocale);

        if (isset($translations[$key]) && trim((string) $translations[$key]) !== '') {
            $text = $translations[$key];
        } else {
            // Fallback to English if active locale translation is missing
            $enTranslations = $activeLocale !== 'en' ? $this->loadJsonTranslations('en') : $translations;
            $text = $enTranslations[$key] ?? $fallback ?? $key;
        }

        // Handle :attribute or {attribute} replacements
        foreach ($replace as $placeholder => $val) {
            $text = str_replace(
                [':' . $placeholder, '{' . $placeholder . '}'],
                (string) $val,
                $text
            );
        }

        return $text;
    }

    /**
     * Update or add a single translation entry into a locale file.
     */
    public function setTranslationKey(string $locale, string $key, string $value): bool
    {
        $translations = $this->loadJsonTranslations($locale);
        $translations[$key] = trim($value);

        return $this->saveJsonTranslations($locale, $translations);
    }

    /**
     * Bulk update translation entries for a given locale.
     *
     * @param array<string, string> $pairs
     */
    public function bulkUpdateTranslations(string $locale, array $pairs): bool
    {
        $translations = $this->loadJsonTranslations($locale);

        foreach ($pairs as $key => $val) {
            if ($val !== null) {
                $translations[$key] = trim($val);
            }
        }

        return $this->saveJsonTranslations($locale, $translations);
    }

    /**
     * Delete a translation key across one or all languages.
     */
    public function deleteTranslationKey(string $key, ?string $locale = null): void
    {
        $locales = $locale ? [$locale] : array_keys($this->getSupportedLanguages());

        foreach ($locales as $loc) {
            $translations = $this->loadJsonTranslations($loc);
            if (isset($translations[$key])) {
                unset($translations[$key]);
                $this->saveJsonTranslations($loc, $translations);
            }
        }
    }

    /**
     * Group translations by domain (menu, form, dashboard, common) for admin UI organization.
     *
     * @return array<string, array<string, string>>
     */
    public function getGroupedTranslations(string $locale): array
    {
        $translations = $this->loadJsonTranslations($locale);
        $grouped = [
            'menu' => [],
            'form' => [],
            'dashboard' => [],
            'common' => [],
            'other' => [],
        ];

        foreach ($translations as $key => $value) {
            $prefix = explode('.', $key)[0] ?? 'other';
            if (isset($grouped[$prefix])) {
                $grouped[$prefix][$key] = $value;
            } else {
                $grouped['other'][$key] = $value;
            }
        }

        return $grouped;
    }

    /**
     * Export complete dictionary formatted for API consumption by Next.js and Mobile app.
     *
     * @return array<string, mixed>
     */
    public function exportLanguageDictionary(?string $locale = null): array
    {
        $locales = $locale ? [$locale] : array_keys($this->getSupportedLanguages());
        $dictionary = [];

        foreach ($locales as $loc) {
            $dictionary[$loc] = [
                'locale' => $loc,
                'meta' => $this->getSupportedLanguages()[$loc] ?? ['code' => $loc],
                'translations' => $this->loadJsonTranslations($loc),
                'updated_at' => File::exists($this->getTranslationFilePath($loc))
                    ? date('c', File::lastModified($this->getTranslationFilePath($loc)))
                    : date('c'),
            ];
        }

        return $locale ? ($dictionary[$locale] ?? []) : $dictionary;
    }
}
