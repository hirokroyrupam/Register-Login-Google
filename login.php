
<?php
session_start();
require "db.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if (isset($_GET["registered"])) {
    $message = "Registration successful! You can now log in.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT id, name, email, password
         FROM users
         WHERE email = ?"
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $user &&
        $user["password"] !== null &&
        password_verify($password, $user["password"])
    ) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["name"];

        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Auth System</title>

    <script src="https://accounts.google.com/gsi/client"
            async defer></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 32px;
            background: #1e293b;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            color: #94a3b8;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 17px;
            border: 1px solid #475569;
            border-radius: 8px;
            background: #0f172a;
            color: white;
            font-size: 15px;
        }

        input:focus {
            outline: 2px solid #818cf8;
            border-color: transparent;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #6366f1;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #4f46e5;
        }

        .message {
            margin-bottom: 16px;
            padding: 10px;
            border-radius: 8px;
            background: #334155;
            color: #e2e8f0;
            font-size: 14px;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
            color: #94a3b8;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #475569;
        }

        .google-button {
            display: flex;
            justify-content: center;
        }

        .footer {
            margin-top: 22px;
            text-align: center;
            color: #cbd5e1;
            font-size: 14px;
        }

        a {
            color: #a5b4fc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Welcome Back</h2>
    <p class="subtitle">Log in to your account</p>

    <?php if ($message !== ""): ?>
        <div class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            autocomplete="email"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Login</button>
    </form>

    <div class="divider">OR</div>

    <div class="google-button">
        <div id="g_id_onload"
             data-client_id="1010045478078-tgp5lobl6ajjp8qp1pvan58t3g8jt88g.apps.googleusercontent.com"
             data-login_uri="http://localhost/auth_system/google_auth.php"
             data-ux_mode="redirect">
        </div>

        <div class="g_id_signin"
             data-type="standard"
             data-shape="rectangular"
             data-theme="outline"
             data-text="continue_with"
             data-size="large"
             data-logo_alignment="left">
        </div>
    </div>

    <p class="footer">
        Don't have an account?
        <a href="register.php">Register</a>
    </p>

</div>
</body>
</html>
