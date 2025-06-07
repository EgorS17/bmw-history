<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"] ?? "";

    // ⚠️ Замени на свой пароль!
    if ($password === "Snv171003") {
        $_SESSION["admin"] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверный пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход администратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #0d1117;
            color: #e6e6e6;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #161b22;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        .form-control {
            background-color: #0d1117;
            color: #e6e6e6;
            border: 1px solid #30363d;
        }

        .btn-primary {
            background-color: #238636;
            border-color: #2ea043;
        }

        .error-text {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h3 class="mb-4 text-center">Вход администратора</h3>
    <?php if (isset($error)): ?>
        <p class="error-text text-center"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Войти</button>
    </form>
</div>
</body>
</html>
