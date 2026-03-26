<?php
session_start();
require 'db.php'; // Aquí ya traemos el objeto $pdo desde tu db.php

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$u = trim($_POST['usuario']);
$p = $_POST['password'];

    // Buscamos al usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    // Verificamos password con el hash de la DB
    if ($user && password_verify($p, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tarjetas Digitales by Hauserver</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink-save: #d81b60;
            --glass: rgba(255, 255, 255, 0.1);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: #0f0c29;
            background: linear-gradient(to bottom, #24243e, #302b63, #0f0c29);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: white;
        }
        .login-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 380px;
            text-align: center;
        }
        .login-card h2 {
            margin-bottom: 25px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 12px;
            border: none;
            background: rgba(255,255,255,0.08);
            color: white;
            outline: none;
            margin-top: 5px;
            box-sizing: border-box;
        }
        input::placeholder { color: rgba(255,255,255,0.5); }
        
        .btn-login {
            background: var(--pink-save);
            color: white;
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-login:hover {
            transform: scale(1.03);
            box-shadow: 0 0 20px rgba(216, 27, 96, 0.4);
        }
        .error-msg {
            color: #ff4d4d;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }
        .logo-h {
            font-size: 0.7rem;
            margin-top: 20px;
            opacity: 0.6;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>HAUSERVER</h2>
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <input type="text" name="usuario" placeholder="Usuario" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
        
        <div class="logo-h">Tarjetas Digitales</div>
    </div>

</body>
</html>
