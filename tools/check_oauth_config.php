<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
// Boot the HTTP kernel so config and providers are registered
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$httpKernel->bootstrap();

// Get services config for google and github
$config = $app['config']->get('services');
echo "services config:\n";
var_export($config);
