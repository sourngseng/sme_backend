<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Traits\TraitLanguage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageController extends Controller
{
    use TraitLanguage;

    /**
     * Display the Language & Translation Management dashboard.
     */
    public function index(Request $request): View
    {
        $currentLocale = $request->query('locale', session('locale', 'en'));
        $languages = $this->getSupportedLanguages();

        if (! isset($languages[$currentLocale])) {
            $currentLocale = 'en';
        }

        $enTranslations = $this->loadJsonTranslations('en');
        $kmTranslations = $this->loadJsonTranslations('km');

        // Merge all distinct keys
        $allKeys = array_unique(array_merge(array_keys($enTranslations), array_keys($kmTranslations)));
        sort($allKeys);

        $selectedCategory = $request->query('category', 'all');
        $search = strtolower(trim((string) $request->query('search', '')));

        $translationList = [];
        foreach ($allKeys as $key) {
            $prefix = explode('.', $key)[0] ?? 'other';

            if ($selectedCategory !== 'all' && $prefix !== $selectedCategory) {
                continue;
            }

            $enVal = $enTranslations[$key] ?? '';
            $kmVal = $kmTranslations[$key] ?? '';

            if ($search !== '') {
                $keyMatch = str_contains(strtolower($key), $search);
                $enMatch = str_contains(strtolower($enVal), $search);
                $kmMatch = str_contains(strtolower($kmVal), $search);
                if (! $keyMatch && ! $enMatch && ! $kmMatch) {
                    continue;
                }
            }

            $translationList[] = [
                'key' => $key,
                'category' => $prefix,
                'en' => $enVal,
                'km' => $kmVal,
            ];
        }

        $stats = [
            'total_keys' => count($allKeys),
            'en_count' => count($enTranslations),
            'km_count' => count($kmTranslations),
            'completion_pct' => count($allKeys) > 0 ? round((count($kmTranslations) / count($allKeys)) * 100) : 100,
        ];

        return view('dashboards.languages', compact(
            'languages',
            'currentLocale',
            'selectedCategory',
            'search',
            'translationList',
            'stats'
        ));
    }

    /**
     * Switch active interface language (Session + Locale).
     */
    public function switchLocale(Request $request): RedirectResponse
    {
        $request->validate([
            'locale' => ['required', 'string', 'in:en,km'],
        ]);

        $locale = $request->input('locale');
        session(['locale' => $locale]);
        app()->setLocale($locale);

        $languages = $this->getSupportedLanguages();
        $langName = $languages[$locale]['name'] ?? $locale;

        return back()->with('status', "Language switched to {$langName} successfully.");
    }

    /**
     * Update an individual translation key or create new entry in JSON files.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string'],
            'en' => ['required', 'string'],
            'km' => ['nullable', 'string'],
        ]);

        $key = trim($validated['key']);
        $enVal = trim($validated['en']);
        $kmVal = trim((string)($validated['km'] ?? ''));

        $this->setTranslationKey('en', $key, $enVal);
        if ($kmVal !== '') {
            $this->setTranslationKey('km', $key, $kmVal);
        }

        return back()->with('status', "Translation key '{$key}' updated successfully.");
    }

    /**
     * Bulk save translation table from UI.
     */
    public function bulkSave(Request $request): RedirectResponse
    {
        $translations = $request->input('translations', []);

        if (is_array($translations)) {
            $enUpdates = [];
            $kmUpdates = [];

            foreach ($translations as $key => $vals) {
                if (isset($vals['en'])) {
                    $enUpdates[$key] = $vals['en'];
                }
                if (isset($vals['km'])) {
                    $kmUpdates[$key] = $vals['km'];
                }
            }

            if (! empty($enUpdates)) {
                $this->bulkUpdateTranslations('en', $enUpdates);
            }
            if (! empty($kmUpdates)) {
                $this->bulkUpdateTranslations('km', $kmUpdates);
            }
        }

        return back()->with('status', 'All translations saved to JSON files successfully.');
    }

    /**
     * JSON API Endpoint for Next.js web frontend and Mobile App.
     */
    public function apiTranslations(?string $locale = null): JsonResponse
    {
        $supported = array_keys($this->getSupportedLanguages());

        if ($locale && ! in_array($locale, $supported)) {
            return response()->json([
                'success' => false,
                'message' => "Unsupported locale '{$locale}'. Supported: " . implode(', ', $supported),
            ], 404);
        }

        $data = $this->exportLanguageDictionary($locale);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
