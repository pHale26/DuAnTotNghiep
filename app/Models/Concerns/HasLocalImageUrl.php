<?php

namespace App\Models\Concerns;

/**
 * Fix image URL paths stored as "/storage/..." to work correctly
 * regardless of APP_URL config, including XAMPP subdirectory setups
 * where APP_URL may be set to just "http://localhost".
 */
trait HasLocalImageUrl
{
    /**
     * Convert a stored image path (e.g. "/storage/tours/file.jpg")
     * to a fully-qualified URL resolved against the real request base.
     * Absolute URLs (e.g. Unsplash) are returned unchanged.
     */
    public function resolveImageUrl(?string $value): ?string
    {
        if (! $value) {
            return $value;
        }

        // Already an absolute URL (http/https) — leave as-is.
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $base = $this->detectBaseUrl();

        return $base.'/'.ltrim($value, '/');
    }

    /**
     * Detect the true public base URL, accounting for XAMPP subdirectory
     * deployments where APP_URL = "http://localhost" but the app lives at
     * "http://localhost/travel/public".
     *
     * Priority:
     *  1. Real HTTP request base (scheme + host + SCRIPT_NAME directory)
     *  2. APP_URL config as fallback (CLI / queue context)
     */
    private function detectBaseUrl(): string
    {
        try {
            $request = request();
            $host = $request->getSchemeAndHttpHost(); // e.g. http://localhost

            // Symfony's getBaseUrl() returns the subdirectory path when Apache
            // is configured with virtual hosts, but is often empty on XAMPP.
            $baseUrl = $request->getBaseUrl(); // e.g. "/travel/public" or ""

            if ($baseUrl !== '') {
                return $host.$baseUrl;
            }

            // Fallback: derive from SCRIPT_NAME (works reliably on XAMPP).
            // SCRIPT_NAME = "/travel/public/index.php" → base = "/travel/public"
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $subdir = trim(dirname($scriptName), '/.');

            return $subdir !== ''
                ? $host.'/'.$subdir
                : $host;
        } catch (\Throwable) {
            // CLI / queue context — no HTTP request available.
            return rtrim(config('app.url'), '/');
        }
    }
}
