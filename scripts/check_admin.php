<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
// Bootstrap the app (not handling requests)
\Illuminate\Foundation\Bootstrap\HandleExceptions::class;
$u = App\Models\User::where('email', 'admin@gmail.com')->first();
if (!$u) {
    echo "NOT_FOUND\n";
    exit;
}
print_r($u->toArray());
