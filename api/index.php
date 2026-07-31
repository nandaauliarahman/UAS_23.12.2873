<?php

// Vercel demo mode uses a bundled SQLite snapshot and ephemeral runtime storage.
$runtimeDatabase = '/tmp/uas-demo.sqlite';
$bundledDatabase = __DIR__.'/database.sqlite';

$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$_SERVER['VERCEL'] = '1';

$host = $_SERVER['HTTP_X_FORWARDED_HOST']
    ?? $_SERVER['HTTP_HOST']
    ?? getenv('VERCEL_URL')
    ?: 'uas-23-12-2873.vercel.app';

$appUrl = str_starts_with($host, 'http')
    ? $host
    : 'https://'.$host;

if (! file_exists($runtimeDatabase) && file_exists($bundledDatabase)) {
    copy($bundledDatabase, $runtimeDatabase);
}

$runtimeEnvironment = [
    'APP_KEY' => 'base64:nyMSZ/k7neUpLcF4/x67IvV0bwD4eoYQr0LuErcSFL4=',
    'APP_URL' => $appUrl,
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

if (! getenv('GOOGLE_REDIRECT_URI')) {
    putenv('GOOGLE_REDIRECT_URI='.$appUrl.'/auth/google/callback');
    $_ENV['GOOGLE_REDIRECT_URI'] = $appUrl.'/auth/google/callback';
    $_SERVER['GOOGLE_REDIRECT_URI'] = $appUrl.'/auth/google/callback';
}

require __DIR__.'/../public/index.php';
