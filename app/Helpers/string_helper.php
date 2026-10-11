<?php

declare(strict_types=1);

if (!function_exists('toAlphanumeric')) {
    /**
     * Wrap a string with single quotes safely.
     *
     * This function trims whitespace and removes any existing leading or trailing
     * single quotes from the input string before wrapping it cleanly in single quotes.
     * Useful for formatting values in dynamic SQL or specific string building.
     *
     * @param string|null $str The input string to be wrapped.
     * @return string The formatted string wrapped in single quotes.
     */
    function toAlphanumeric(?string $str = null): string {
        $str = trim($str ?? '');
        $str = ltrim($str, "'");
        $str = rtrim($str, "'");
        return "'" . $str . "'";
    }
}

if (!function_exists('toAlphanumericAllowed')) {
    /**
     * Wrap a string with double quotes safely.
     *
     * This function trims whitespace and removes any existing leading or trailing
     * double quotes from the input string before wrapping it cleanly in double quotes.
     * Frequently used for enforcing JSON key formatting or strict string parsing.
     *
     * @param string|null $str The input string to be wrapped.
     * @return string The formatted string wrapped in double quotes.
     */
    function toAlphanumericAllowed(?string $str = null): string {
        $str = trim($str ?? '');
        $str = ltrim($str, '"');
        $str = rtrim($str, '"');
        return '"' . $str . '"';
    }
}
