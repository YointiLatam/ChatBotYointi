<?php

/**
 * CORS origin matching, kept free of globals so it can be unit-tested without a web server.
 */
class Cors {
    /**
     * Value for Access-Control-Allow-Origin, or null when the origin must not be allowed.
     *
     * @param string|null $allowedOrigins "*" or a comma-separated list of exact origins
     * @param string|null $requestOrigin  Origin header sent by the browser (may be empty)
     */
    public static function allowOriginValue($allowedOrigins, $requestOrigin) {
        $allowedOrigins = trim((string)$allowedOrigins);
        if ($allowedOrigins === '*') {
            return '*';
        }
        $requestOrigin = trim((string)$requestOrigin);
        if ($requestOrigin === '') {
            return null;
        }
        $origins = array_filter(array_map('trim', explode(',', $allowedOrigins)), 'strlen');
        return in_array($requestOrigin, $origins, true) ? $requestOrigin : null;
    }

    /**
     * Send the CORS headers for the current request. With an origin list the response depends
     * on the Origin header, so caches must be told to vary on it.
     */
    public static function sendHeaders($allowedOrigins, $requestOrigin) {
        $value = self::allowOriginValue($allowedOrigins, $requestOrigin);
        if (trim((string)$allowedOrigins) !== '*') {
            header('Vary: Origin', false);
        }
        if ($value !== null) {
            header('Access-Control-Allow-Origin: ' . $value);
        }
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
    }
}
