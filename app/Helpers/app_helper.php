<?php

declare(strict_types=1);

if (!function_exists('toJson')) {
    /**
     * Safely encode a variable into a JSON string.
     *
     * Utilizes strict JSON encoding flags to prevent XSS (JSON_HEX_TAG, JSON_HEX_APOS, etc.)
     * and ensures robust error handling via JSON_THROW_ON_ERROR.
     *
     * @param mixed $arr The variable (array, object, etc.) to encode.
     * @return string|false JSON formatted string, or false on failure.
     * @throws JsonException If the encoding process fails.
     */
    function toJson(mixed $arr = null): false|string {
        return json_encode($arr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    }
}
