<?php

// Vercel demo mode uses a bundled SQLite snapshot and ephemeral runtime storage.
$runtimeDatabase = '/tmp/uas-demo.sqlite';
$bundledDatabase = __DIR__.'/database.sqlite';

if (! file_exists($runtimeDatabase) && file_exists($bundledDatabase)) {
    copy($bundledDatabase, $runtimeDatabase);
}

putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE='.$runtimeDatabase);
putenv('SESSION_DRIVER=array');
putenv('CACHE_STORE=array');
putenv('APP_URL=https://uas-23-12-2873.vercel.app');

require __DIR__.'/../public/index.php';
