<?php
session_start();
$host = 'db';
$db   = 'tarjetas_db';
$user = 'tarjetas_user';
$pass = 'tarjetas_pass';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['usuario'];
    $p = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();

    // Verificación simple (para tu ejemplo cliente1/cliente1)
    if ($user && $p === 'cliente1') { // Aquí luego usaremos password_verify
        $_SESSION['admin_logged'] = true;
        $_SESSION['user_id'] = $user['id'];
        header("Location: admin.php");
        exit;
    } else {
        $error = "Usuario o clave incorrectos";
    }
}
?>
