<?php
$path = "routes/web.php";
$content = file_get_contents($path);
if (strpos($content, "/api/check-email") === false) {
    $route = "
Route::post(\"/api/check-email\", function (\\Illuminate\\Http\\Request \$request) {
    \$request->validate([\"email\" => \"required|email\"]);
    \$exists = \\App\\Models\\User::where(\"email\", \$request->email)->exists();
    return response()->json([\"exists\" => \$exists]);
})->name(\"api.check-email\");
";
    $content .= $route;
    file_put_contents($path, $content);
    echo "Added route.\n";
} else {
    echo "Route already exists.\n";
}
