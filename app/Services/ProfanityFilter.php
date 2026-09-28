<?php

namespace App\Services;

class ProfanityFilter
{
    protected static $badWords = [
        'địt', 'lồn', 'cặc', 'buồi', 'đĩ', 'phò', 'đéo', 'vcl', 'vl', 'đm', 'đmm', 'vãi lồn', 'con cặc', 'cái lồn', 'chó đẻ', 'mả mẹ', 'con đĩ', 'thằng chó', 'đù', 'đậu xanh', 'cmn', 'đkm', 'vãi', 'sml', 'cc', 'cl', 'clgt', 'ml',
    ];

    /**
     * Lọc và che các từ ngữ tục tĩu
     */
    public static function filter(?string $text): ?string
    {
        if (! $text) {
            return $text;
        }

        $filteredText = $text;

        // Sort by length descending to replace longer phrases first (e.g. 'con cặc' before 'cặc')
        $words = self::$badWords;
        usort($words, function ($a, $b) {
            return mb_strlen($b) - mb_strlen($a);
        });

        foreach ($words as $word) {
            // Regex to match exact word with case insensitivity
            // \b boundary might not work well with utf-8 Vietnamese, so we use a simpler str_ireplace or regex with unicode
            $pattern = '/\b'.preg_quote($word, '/').'\b/iu';

            // Generate asterisks based on word length
            $replacement = str_repeat('*', mb_strlen($word));

            $filteredText = preg_replace($pattern, $replacement, $filteredText);
        }

        return $filteredText;
    }
}
