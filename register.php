
<?php
require "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8
    ) {
        $message = "Enter a valid name, email, and password of at least 8 characters.";
        $messageType = "error";
    } else {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword
            ]);

            header("Location: login.php?registered=1");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $message = "This email is already registered.";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Auth System</title>

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
            background: #7f1d1d;
            color: #fecaca;
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

    <h2>Create Account</h2>
    <p class="subtitle">Register to get started</p>

    <?php if ($message !== ""): ?>
        <div class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="name">Full Name</label>
        <input
            type="text"
            id="name"
            name="name"
            placeholder="Enter your full name"
            maxlength="100"
            autocomplete="name"
            required
        >

        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            maxlength="150"
            autocomplete="email"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            placeholder="At least 8 characters"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <button type="submit">Register</button>
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
        Already have an account?
        <a href="login.php">Login</a>
    </p>

</div>
</body>
</html>
