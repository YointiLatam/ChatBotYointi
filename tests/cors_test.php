<?php

require_once __DIR__ . '/../backend/Cors.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

$list = 'https://yointi.com, https://www.yointi.com';

// Wildcard allows everything, with or without an Origin header.
assertSameValue('*', Cors::allowOriginValue('*', 'https://evil.example'), 'Wildcard should allow any origin.');
assertSameValue('*', Cors::allowOriginValue('*', ''), 'Wildcard should work without an Origin header.');

// A list echoes only exact matches (whitespace around entries is ignored).
assertSameValue('https://yointi.com', Cors::allowOriginValue($list, 'https://yointi.com'), 'Apex origin should be echoed.');
assertSameValue('https://www.yointi.com', Cors::allowOriginValue($list, 'https://www.yointi.com'), 'www origin should be echoed.');

// Everything else is rejected: other hosts, scheme/port changes, suffix tricks, trailing slash.
assertSameValue(null, Cors::allowOriginValue($list, 'https://evil.example'), 'Unlisted origin must be rejected.');
assertSameValue(null, Cors::allowOriginValue($list, 'http://yointi.com'), 'Scheme mismatch must be rejected.');
assertSameValue(null, Cors::allowOriginValue($list, 'https://yointi.com:8443'), 'Port mismatch must be rejected.');
assertSameValue(null, Cors::allowOriginValue($list, 'https://yointi.com.evil.example'), 'Suffix trick must be rejected.');
assertSameValue(null, Cors::allowOriginValue($list, 'https://yointi.com/'), 'Trailing slash is not a valid origin.');
assertSameValue(null, Cors::allowOriginValue($list, ''), 'Missing Origin must not be allowed with a list.');
assertSameValue(null, Cors::allowOriginValue($list, null), 'Null Origin must not be allowed with a list.');
assertSameValue(null, Cors::allowOriginValue('', 'https://yointi.com'), 'Empty config allows nothing.');
assertSameValue(null, Cors::allowOriginValue('https://yointi.com,,', ''), 'Empty list entries must not match an empty Origin.');

echo "CORS tests passed.\n";
