<?php

// Vercel demo mode uses a bundled SQLite snapshot and ephemeral runtime storage.
$runtimeDatabase = '/tmp/uas-demo.sqlite';
$bundledDatabase = __DIR__.'/database.sqlite';

if (! file_exists($runtimeDatabase) && file_exists($bundledDatabase)) {
    copy($bundledDatabase, $runtimeDatabase);
}

putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE='.$runtimeDatabase);
putenv('SESSION_DRIVER=cookie');
putenv('SESSION_COOKIE=uas_demo_session');
putenv('SESSION_SECURE_COOKIE=true');
putenv('SESSION_SAME_SITE=lax');
putenv('CACHE_STORE=array');
putenv('APP_URL=https://uas-23-12-2873.vercel.app');

require __DIR__.'/../public/index.php';
