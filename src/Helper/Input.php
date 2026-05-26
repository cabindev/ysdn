<?php
namespace App\Helper;

class Input {
    /**
     * Clean a general text field — strip tags, trim, collapse whitespace.
     */
    public static function text(mixed $value): string {
        return trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)));
    }

    /**
     * Allow only digits and + - ( ) spaces (phone numbers).
     */
    public static function phone(mixed $value): string {
        return preg_replace('/[^0-9+\-() ]/', '', (string) $value);
    }

    /**
     * Validate and return a clean email, or empty string.
     */
    public static function email(mixed $value): string {
        $clean = filter_var(trim((string) $value), FILTER_SANITIZE_EMAIL);
        return filter_var($clean, FILTER_VALIDATE_EMAIL) ? $clean : '';
    }

    /**
     * Allow only digits.
     */
    public static function digits(mixed $value): string {
        return preg_replace('/[^0-9]/', '', (string) $value);
    }

    /**
     * Allow only alphanumeric + hyphens (codes, slugs).
     */
    public static function alphanumeric(mixed $value): string {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '', (string) $value);
    }

    /**
     * Sanitize an array of POST fields using a rules map.
     *
     * Rules: 'text' | 'phone' | 'email' | 'digits' | 'alphanumeric' | 'int'
     *
     * Usage:
     *   $data = Input::sanitizePost([
     *       'name'  => 'text',
     *       'phone' => 'phone',
     *       'email' => 'email',
     *       'age'   => 'int',
     *   ]);
     */
    public static function sanitizePost(array $rules): array {
        $result = [];
        foreach ($rules as $field => $type) {
            $raw = $_POST[$field] ?? '';
            $result[$field] = match($type) {
                'phone'        => self::phone($raw),
                'email'        => self::email($raw),
                'digits'       => self::digits($raw),
                'alphanumeric' => self::alphanumeric($raw),
                'int'          => (int) $raw,
                default        => self::text($raw),  // 'text'
            };
        }
        return $result;
    }

    /**
     * Safe HTML output — always use when echoing user-supplied data.
     */
    public static function e(mixed $value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
