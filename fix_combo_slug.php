<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Update combo slugs
DB::statement("UPDATE combo SET slug = CONCAT('combo-', id) WHERE slug IS NULL OR slug = ''");

echo "✅ Đã cập nhật slug cho combo!\n";
