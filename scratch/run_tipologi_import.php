<?php

use App\Http\Controllers\Operator\TipologiController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Boot Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Sign in a user to satisfy Auth::id()
$user = User::first();
if ($user) {
    Auth::login($user);
    echo "Logged in as user ID: " . $user->id . "\n";
} else {
    echo "Warning: No user found in database!\n";
}

// Read the payload
$payloadJson = file_get_contents(__DIR__ . '/parsed_payload.json');
$payload = json_decode($payloadJson, true);
echo "Loaded " . count($payload) . " payload items.\n";

$controller = new TipologiController();

// Simulate import method
$successCount = 0;
$skippedCount = 0;

foreach ($payload as $idx => $item) {
    $resolved = $controller->resolveRegionNamesPublic($item); // We will need to make this public or call it via reflection/subclass
    // Wait, let's write a helper class or reflection to access protected methods
}
echo "Done.\n";
