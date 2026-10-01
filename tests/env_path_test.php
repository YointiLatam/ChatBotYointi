<?php

require_once __DIR__ . '/../backend/config.php';

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        throw new RuntimeException($message . "\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true));
    }
}

// Mimics the hosting layout: <site>/.env (outside the web root) and <site>/public_html (the project).
$site = sys_get_temp_dir() . '/yointi_env_test_' . bin2hex(random_bytes(4));
$project = $site . '/public_html';
mkdir($project, 0777, true);

try {
    // No .env anywhere: falls back to the project-root path (even if the file does not exist).
    assertSameValue($project . '/.env', findEnvFile($project), 'Without any .env it should point to the project root.');

    // Only the project copy exists: it is used.
    file_put_contents($project . '/.env', "K=project\n");
    assertSameValue($project . '/.env', findEnvFile($project), 'The project .env should be used when it is the only one.');

    // Both exist: the one above the project root wins.
    file_put_contents($site . '/.env', "K=outside\n");
    assertSameValue($site . '/.env', findEnvFile($project), 'The .env outside the web root must win.');

    // Trailing slash on the project root does not change the result.
    assertSameValue($site . '/.env', findEnvFile($project . '/'), 'A trailing slash must not matter.');

    // Only the outside copy exists: it is used.
    unlink($project . '/.env');
    assertSameValue($site . '/.env', findEnvFile($project), 'The outside .env should be used when it is the only one.');

    // And its values are parsed.
    $values = loadEnv(findEnvFile($project));
    assertSameValue('outside', $values['K'] ?? null, 'The selected file must be the one that is parsed.');
} finally {
    @unlink($project . '/.env');
    @unlink($site . '/.env');
    @rmdir($project);
    @rmdir($site);
}

echo "Env path tests passed.\n";
