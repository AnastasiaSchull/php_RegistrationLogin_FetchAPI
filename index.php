

<?php
session_start();

$welcomeMessage = "Welcome to the site ";

if (isset($_SESSION['user'])) {
    $welcomeMessage .= ",  <span>" . htmlspecialchars($_SESSION['user']) . "</span>!";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Home page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1 class="welcome"><?php echo  $welcomeMessage; ?></h1>

<p><a href="registration.php">Registration</a></p>
<p><a href="login.php">Login</a></p>
</body>
</html>
