<?php

// Vercel demo mode uses a bundled SQLite snapshot and ephemeral runtime storage.
$runtimeDatabase = '/tmp/uas-demo.sqlite';
$bundledDatabase = __DIR__.'/database.sqlite';

$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$_SERVER['VERCEL'] = '1';

if (! file_exists($runtimeDatabase) && file_exists($bundledDatabase)) {
    copy($bundledDatabase, $runtimeDatabase);
}

$runtimeEnvironment = [
    'APP_KEY' => 'base64:nyMSZ/k7neUpLcF4/x67IvV0bwD4eoYQr0LuErcSFL4=',
    'APP_URL' => 'https://uas-23-12-2873.vercel.app',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => $runtimeDatabase,
    'SESSION_DRIVER' => 'cookie',
    'SESSION_COOKIE' => 'uas_demo_session',
    'SESSION_SECURE_COOKIE' => 'true',
    'SESSION_SAME_SITE' => 'lax',
    'CACHE_STORE' => 'array',
];

foreach ($runtimeEnvironment as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
}

require __DIR__.'/../public/index.php';
