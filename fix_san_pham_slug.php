<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Update san_pham slugs
DB::statement("UPDATE san_pham SET slug = CONCAT('san-pham-', id) WHERE slug IS NULL OR slug = ''");

echo "✅ Đã cập nhật slug cho san_pham!\n";
