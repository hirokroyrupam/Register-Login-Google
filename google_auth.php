```php
<?php
session_start();

require "db.php";
require "vendor/autoload.php";

$clientId = "1010045478078-tgp5lobl6ajjp8qp1pvan58t3g8jt88g.apps.googleusercontent.com";

// Verify Google's CSRF protection
if (
    !isset($_POST["g_csrf_token"], $_COOKIE["g_csrf_token"]) ||
    !hash_equals(
        $_COOKIE["g_csrf_token"],
        $_POST["g_csrf_token"]
    )
) {
    http_response_code(400);
    exit("Invalid request. Please try again.");
}

// Get the Google credential
$idToken = $_POST["credential"] ?? "";

if ($idToken === "") {
    exit("Google credential is missing.");
}

// Verify Google's ID token
$client = new Google\Client([
    "client_id" => $clientId
]);

try {
    $payload = $client->verifyIdToken($idToken);
} catch (Throwable $e) {
    error_log("Google verification error: " . $e->getMessage());
    exit("Google verification error. Check the PHP error log.");
}

if (!$payload) {
    exit("Token verification failed. Check your Client ID and server connectivity.");
}

// Check verified email
if (empty($payload["email_verified"])) {
    exit("Your Google email must be verified.");
}

$googleId = $payload["sub"];
$email = $payload["email"];
$name = $payload["name"] ?? "Google User";

// Find an existing Google account
$stmt = $pdo->prepare(
    "SELECT id, name, email
     FROM users
     WHERE google_id = ?"
);

$stmt->execute([$googleId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Register the Google user if necessary
if (!$user) {
    $stmt = $pdo->prepare(
        "SELECT id FROM users WHERE email = ?"
    );

    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        exit(
            "An account with this email already exists. " .
            "Please log in using your existing method."
        );
    }

    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password, google_id)
         VALUES (?, ?, NULL, ?)"
    );

    $stmt->execute([$name, $email, $googleId]);

    $userId = $pdo->lastInsertId();
} else {
    $userId = $user["id"];
    $name = $user["name"];
}

// Create the login session
session_regenerate_id(true);

$_SESSION["user_id"] = $userId;
$_SESSION["user_name"] = $name;

header("Location: dashboard.php");
exit;
?>
```