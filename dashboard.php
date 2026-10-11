
<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome,
        <?= htmlspecialchars($_SESSION["user_name"]) ?>!
    </h1>

    <p>You have successfully logged in.</p>

    <a href="logout.php">Logout</a>
</body>
</html>
