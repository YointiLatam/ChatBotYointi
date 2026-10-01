<?php

require_once __DIR__ . '/../backend/RateLimiter.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

function assertTrue($condition, $message) {
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$dbFile = sys_get_temp_dir() . '/ratelimit_test_' . getmypid() . '.sqlite';
RateLimiter::useDatabase($dbFile);

try {
    $ip = '203.0.113.10';
    $limit = MAX_DAILY_QUERIES;

    // Reserve + confirm consumes one query.
    $r = RateLimiter::reserve($ip);
    assertTrue($r['reserved'] && $r['allowed'], 'The first reservation should succeed.');
    $s = RateLimiter::confirm($ip);
    assertSameValue(1, $s['current'], 'A confirmed reservation should consume one query.');
    assertSameValue($limit - 1, $s['remaining'], 'Remaining should decrease after confirmation.');

    // Reserve + release leaves the count unchanged (simulated answer or failure).
    $r = RateLimiter::reserve($ip);
    $s = RateLimiter::release($ip, $r['date']);
    assertSameValue(1, $s['current'], 'A released reservation should not consume a query.');
    assertSameValue($limit - 1, $s['remaining'], 'Remaining should be unchanged after a release.');

    // Fill up to the limit; the next reservation must fail.
    for ($i = 1; $i < $limit; $i++) {
        assertTrue(RateLimiter::reserve($ip)['reserved'], "Reservation $i should succeed below the limit.");
    }
    $s = RateLimiter::checkLimit($ip);
    assertSameValue($limit, $s['current'], 'The counter should reach the limit.');
    assertSameValue(0, $s['remaining'], 'No queries should remain at the limit.');
    $over = RateLimiter::reserve($ip);
    assertSameValue(false, $over['reserved'], 'Reserving beyond the limit should fail.');
    assertSameValue(false, $over['allowed'], 'Reserving beyond the limit should not be allowed.');
    assertSameValue($limit, RateLimiter::checkLimit($ip)['current'], 'A failed reservation must not change the count.');

    // Releasing after the limit frees exactly one slot.
    $s = RateLimiter::release($ip, $over['date']);
    assertSameValue($limit - 1, $s['current'], 'A release should free one slot.');
    assertTrue(RateLimiter::reserve($ip)['reserved'], 'A freed slot should be reservable again.');

    // Release never goes below zero and is isolated per IP.
    $other = '203.0.113.20';
    $s = RateLimiter::release($other);
    assertSameValue(0, $s['current'], 'Releasing without a reservation should not go negative.');
    RateLimiter::release($other);
    assertSameValue(0, RateLimiter::checkLimit($other)['current'], 'Repeated releases should stay at zero.');
    assertSameValue($limit, RateLimiter::checkLimit($ip)['current'], 'Releasing another IP must not touch this IP.');

    // Simulated-answer flow: reserve, release, status unchanged.
    $before = RateLimiter::checkLimit($other);
    $r = RateLimiter::reserve($other);
    RateLimiter::release($other, $r['date']);
    $after = RateLimiter::checkLimit($other);
    assertSameValue($before['remaining'], $after['remaining'], 'A simulated answer should leave remaining unchanged.');
    assertSameValue($before['current'], $after['current'], 'A simulated answer should leave current unchanged.');

    echo "Rate limiter tests passed.\n";
} finally {
    foreach ([$dbFile, $dbFile . '-wal', $dbFile . '-shm'] as $f) {
        if (file_exists($f)) {
            unlink($f);
        }
    }
}
