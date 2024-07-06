<?php
session_start();
//страница логина, обрабатывает запросы и возвращает ответы в формате JSON
if ($_SERVER["REQUEST_METHOD"] == "POST") {
header('Content-Type: application/json');
include_once 'config.php';
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit;
}
$username = isset($data['username']) ? $data['username'] : null;
$password = isset($data['password']) ? $data['password'] : null;

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username or password missing']);
    exit;
}
$connection = getDbConnection();


$query = $connection->prepare("SELECT username, password FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        $_SESSION['user'] = $username;
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Incorrect password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

$query->close();
$connection->close();
} else {
// отправляем HTML только если это GET запрос
header('Content-Type: text/html');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="form-container">
<h1>Login</h1>
<form id="login-form">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
    <div id="message" style="color: red; margin-top: 10px;"></div>
</div>
<p><a href="index.php">Back</a></p>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("login-form");
            const messageDiv = document.getElementById("message");
            form.addEventListener("submit", function(event) {
                event.preventDefault();
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => { data[key] = value; });

                fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        //alert(data.message);
                        messageDiv.textContent = data.message;
                        if (data.success) {
                            window.location.href = 'index.php';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>

</body>
</html>
    <?php
}
?>
