<?php
require 'db.php';

try {
    // 1. Crear tabla con columna JSON
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        config_json LONGTEXT -- Guardamos el JSON completo aquí
    );";
    $pdo->exec($sql);

    // 2. Datos por defecto (los que tenías en tu config.json)
    $default_config = [
        "nombre" => "Leyla",
        "frase_hero" => "Mis dulces 15",
        "fecha" => "2026-04-03",
        "hora" => "21:00",
        "lugar_nombre" => "Multi Espacio Pacheco",
        "lugar_direccion" => "Santiago del Estero 185, Gral. Pacheco",
        "lugar_mapa" => "https://goo.gl/maps/xyz",
        "dress_code" => "Elegante Sport",
        "alias" => "mailen9006",
        "titular" => "Nombre del Titular",
        "whatsapp_confirmacion" => "5491128489352",
        "color_titulos" => "#ff0080",
        "color_frases" => "#0080c0",
        "color_texto_base" => "#444444",
        "color_fondo" => "#000000",
        "frase_dress" => "Te esperamos con tu mejor look para celebrar juntos.", // Asegúrate que esté
        "frase_regalo" => "Mi mayor alegría es compartir este día contigo.", // Asegúrate que esté
        "frase_final" => "Tu presencia ayudará a que mi noche sea inolvidable" // Asegúrate que esté
    ];

    $json_data = json_encode($default_config);
    $pass_hash = password_hash('cliente1', PASSWORD_BCRYPT);

    // 3. Insertar usuario inicial si no existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = 'cliente1'");
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        $ins = $pdo->prepare("INSERT INTO usuarios (usuario, password, config_json) VALUES (?, ?, ?)");
        $ins->execute(['cliente1', $pass_hash, $json_data]);
        echo "✅ Instalación exitosa. Tabla creada y 'cliente1' configurado.";
    } else {
        echo "ℹ️ El sistema ya está instalado.";
    }

} catch (Exception $e) {
    die("❌ Error en la instalación: " . $e->getMessage());
}
?>
