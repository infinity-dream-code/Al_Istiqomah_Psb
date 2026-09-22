<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/ServiceLogin', 'GET');
$response = $kernel->handle($request);
echo 'STATUS=' . $response->getStatusCode() . PHP_EOL;
$content = $response->getContent();
echo 'LEN=' . strlen($content) . PHP_EOL;
if ($response->getStatusCode() >= 400) {
    echo $content . PHP_EOL;
} else {
    echo (str_contains($content, 'FORM LOGIN') ? "HAS_LOGIN_FORM\n" : "NO_LOGIN_FORM\n");
    echo (str_contains($content, 'session-guard') ? "HAS_GUARD\n" : "NO_GUARD_REF\n");
}
$kernel->terminate($request, $response);
