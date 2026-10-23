<?php

if (! function_exists('transLabel')) {
    /**
     * Translate a menu, form, or UI label using TraitLanguage.
     *
     * @param string $key
     * @param array<string, mixed>|string|null $replaceOrFallback
     * @param array<string, mixed>|string|null $localeOrReplace
     * @param string|null $locale
     * @return string
     */
    function transLabel(string $key, array|string|null $replaceOrFallback = [], array|string|null $localeOrReplace = null, ?string $locale = null): string
    {
        return app(\App\Services\LanguageService::class)->transLabel($key, $replaceOrFallback, $localeOrReplace, $locale);
    }
}
