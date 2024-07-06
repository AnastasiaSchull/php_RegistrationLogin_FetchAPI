<?php
session_start();

include_once 'config.php';
$connection = getDbConnection();

header('Content-Type: application/json');

// проверка наличия пользователя по GET запросу
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['username'])) {
    $username = $_GET['username'];
    $query = $connection->prepare("SELECT id FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
    $query->close();
    $connection->close();
    exit;
}

// обработка регистрации пользователя по POST запросу
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data['username'];
    $password = $data['password'];

    $query = $connection->prepare("SELECT id FROM users WHERE username = ?");
    $query->bind_param("s", $username);//bind_param привязывает  значение переменной к плейсхолдеру
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $query->close();
        $connection->close();
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert = $connection->prepare("INSERT INTO users (username, password) VALUES (?, ?)");//лейсхолдеры в подготовленных запросах (prepared statements) используются для указания мест, где будут подставлены реальные значения при выполнении запроса к БД
        $insert->bind_param("ss", $username, $hashed_password);//ss- два параметра и оба string
        $insert->execute();

        if ($insert->affected_rows > 0) {
            $_SESSION['user'] = $username;
            $insert->close();
            $connection->close();
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            $insert->close();
            $connection->close();
            echo json_encode(['success' => false, 'message' => 'Error during registration']);
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // oтправляем HTML только если это GET и без параметра username
    header('Content-Type: text/html');
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Registration</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <div class="form-container">
    <h1>Registration</h1>
    <form id="registration-form">
        Username: <input type="text" name="username" id="username" value="" required><br>
        <span id="usernameFeedback"></span><br>
        Password: <input type="password" name="password" id="password" value="" required><br>

        <input type="submit" value="Register">
    </form>
        <span id="message" style="color: green; display: none;"></span>
    </div>
    <p><a href="index.php">Back</a></p>
    <script src="registration.js"></script>
    </body>
    </html>
    <?php
}
?>
