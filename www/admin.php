<?php
session_start();

// Si no hay sesión, mandalo al login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';
$usuario_logueado = $_SESSION['usuario'];

$user_id = $_SESSION['admin_id'];
$msg = "";

// 2. Leer configuración desde la DB
$stmt = $pdo->prepare("SELECT config_json, usuario FROM usuarios WHERE id = ?");
$stmt->execute([$user_id]);
$usuario_db = $stmt->fetch();

if (!$usuario_db) { die("Error: Usuario no encontrado."); }

$config = json_decode($usuario_db['config_json'], true);
$nombre_usuario = $usuario_db['usuario'];

// 3. Procesar cambios de texto (UPDATE en DB)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_config'])) {
    foreach ($_POST['field'] as $key => $value) { 
        $config[$key] = $value; 
    }
    
    $json_upd = json_encode($config);
    $upd = $pdo->prepare("UPDATE usuarios SET config_json = ? WHERE id = ?");
    
    if($upd->execute([$json_upd, $user_id])) {
        $msg = "Datos Actualizados Correctamente";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['up_file'])) {
    $dest = $_POST['file_name'];
    $user_folder = __DIR__ . "/clientes/" . $_SESSION['usuario'] . "/"; 
    
    if(move_uploaded_file($_FILES['up_file']['tmp_name'], $user_folder . $dest)) {
        echo "Subido Correctamente";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HSTD | Panel de Control</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ff0080;
            --bg-dark: #1a1c24;
            --sidebar-dark: #11131a;
            --card-bg: #252833;
            --text-main: #e2e8f0;
            --text-muted: #94a3b8;
            --input-bg: #2d323e;
            --border: #3d4455;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-dark); color: var(--text-main); display: flex; min-height: 100vh; }

        nav { width: 280px; background: var(--sidebar-dark); padding: 40px 20px; border-right: 1px solid var(--border); position: fixed; height: 100vh; z-index: 100; display: flex; flex-direction: column; }
        .logo-area { display: flex; align-items: center; gap: 12px; margin-bottom: 30px; padding: 0 10px; }
        .logo-area i { color: var(--primary); font-size: 1.5rem; }
        .logo-area h2 { font-size: 1.1rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }

        /* Estilo para el nombre del cliente */
        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.05);
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 0.9rem;
            color: var(--text-main);
            border: 1px solid var(--border);
        }
        .user-badge i { color: var(--primary); }

        nav a {
            color: var(--text-muted); text-decoration: none; padding: 14px 18px; border-radius: 12px;
            display: flex; align-items: center; gap: 12px; font-weight: 500; transition: 0.3s; margin-bottom: 5px; cursor: pointer;
        }
        nav a:hover, nav a.active { background: rgba(255, 255, 255, 0.05); color: #fff; }
        nav a.active { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(255, 0, 128, 0.3); }

	main { flex: 1; margin-left: 280px; padding: 50px; max-width: 1100px; }
        .card { background: var(--card-bg); border-radius: 20px; padding: 30px; border: 1px solid var(--border); margin-bottom: 30px; animation: fadeIn 0.4s ease; }
        .card h3 { font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px; color: var(--primary); display: flex; align-items: center; gap: 10px; }

        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .input-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        .input-group label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }

        input, select, textarea { background: var(--input-bg); border: 1px solid var(--border); padding: 12px; border-radius: 10px; color: white; font-family: inherit; }
        input[type="color"] { height: 50px; padding: 5px; cursor: pointer; background: none; border: 1px solid var(--border); width: 100%; }

        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
        .media-item { background: var(--input-bg); border-radius: 15px; padding: 10px; border: 1px solid var(--border); text-align: center; position: relative; }
        .media-item img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.1); }
        .upload-btn { background: rgba(255,255,255,0.05); border: 1px dashed var(--border); padding: 8px; border-radius: 8px; font-size: 0.7rem; cursor: pointer; display: block; transition: 0.3s; }
        .upload-btn:hover { border-color: var(--primary); color: var(--primary); }

        .btn-save { background: var(--primary); color: white; border: none; padding: 20px; border-radius: 15px; font-weight: 700; cursor: pointer; width: 100%; font-size: 1rem; box-shadow: 0 4px 15px rgba(255, 0, 128, 0.2); transition: 0.3s; margin-top: 20px; }
        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(255, 0, 128, 0.4); }

        .section { display: none; }
        .section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav>
    <div class="logo-area"><i class="fas fa-robot"></i><h2><span>TD by HauseTienda</span></h2></div>
    
    <div class="user-badge">
        <i class="fas fa-user-circle fa-lg"></i>
        <span><b>Hola <?php echo ucfirst($nombre_usuario); ?> !</b></span>
    </div>

    <a onclick="sh('s-info')" id="l-info" class="active"><i class="fas fa-id-card"></i> General</a>
    <a onclick="sh('s-evento')" id="l-evento"><i class="fas fa-map-marker-alt"></i> Evento</a>
    <a onclick="sh('s-multi')" id="l-multi"><i class="fas fa-photo-film"></i> Galeria</a>
    <a onclick="sh('s-regalos')" id="l-regalos"><i class="fas fa-gift"></i> Regalos</a>
    <a onclick="sh('s-frases')" id="l-frases"><i class="fas fa-quote-right"></i> Frases</a>
    
    <div style="margin-top: auto;">
	<a href="index.php?u=<?php echo $nombre_usuario; ?>" target="_blank" style="color:var(--primary)"><i class="fas fa-external-link-alt"></i> Ver Invitacion</a>
        <a href="logout.php" style="color:#ff4d4d; font-size: 0.8rem; opacity: 0.8;"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<main>
    <?php if($msg): ?> 
        <div style="background: rgba(16,185,129,0.1); border: 1px solid #10b981; color:#10b981; padding:15px; border-radius:12px; margin-bottom:20px; animation: fadeIn 0.3s ease;">
            <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
        </div> 
    <?php endif; ?>

    <form method="POST" onsubmit="this.action = window.location.hash;">
        <input type="hidden" name="update_config" value="1">

<div id="s-info" class="section active">
    <div class="card">
        <h3><i class="fas fa-palette"></i> Personalización de Colores</h3>
        <div class="form-grid">
            <div class="input-group">
                <label>Color Nombre Principal y Titulos</label>
                <input type="color" name="field[color_titulos]" value="<?php echo $config['color_titulos'] ?? '#ff0080'; ?>">
            </div>
            <div class="input-group">
                <label>SubTitulos</label>
                <input type="color" name="field[color_frases]" value="<?php echo $config['color_frases'] ?? '#00ffff'; ?>">
            </div>
            <div class="input-group">
                <label>Texto Principal</label>
                <input type="color" name="field[color_texto_base]" value="<?php echo $config['color_texto_base'] ?? '#ffffff'; ?>">
            </div>

            <div class="input-group">
                <label>Fondo General</label>
                <input type="color" name="field[color_fondo]" value="<?php echo $config['color_fondo'] ?? '#000000'; ?>">
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3><i class="fas fa-user"></i> Identidad de la Invitación</h3>
        <div class="form-grid">
            <div class="input-group"><label>Nombre del Agasajado</label><input type="text" name="field[nombre]" value="<?php echo $config['nombre']; ?>"></div>
            <div class="input-group"><label>Slogan/Frase Inicial</label><input type="text" name="field[frase_hero]" value="<?php echo $config['frase_hero']; ?>"></div>
            <div class="input-group"><label>WhatsApp Confirmación</label><input type="text" name="field[whatsapp_confirmacion]" value="<?php echo $config['whatsapp_confirmacion']; ?>"></div>
        </div>
    </div>
</div>

        <div id="s-evento" class="section">
            <div class="card">
                <h3><i class="fas fa-map-location-dot"></i> Ubicación del Evento</h3>
                <div class="form-grid">
                    <div class="input-group"><label>Fecha</label><input type="date" name="field[fecha]" value="<?php echo $config['fecha']; ?>"></div>
                    <div class="input-group"><label>Hora</label><input type="time" name="field[hora]" value="<?php echo $config['hora']; ?>"></div>
                    <div class="input-group" style="grid-column: span 2;"><label>Lugar</label><input type="text" name="field[lugar_nombre]" value="<?php echo $config['lugar_nombre']; ?>"></div>
                    <div class="input-group" style="grid-column: span 2;"><label>Dirección</label><input type="text" name="field[lugar_direccion]" value="<?php echo $config['lugar_direccion']; ?>"></div>
                    <div class="input-group" style="grid-column: span 2;"><label>Link Maps</label><input type="text" name="field[lugar_mapa]" value="<?php echo $config['lugar_mapa']; ?>"></div>
                </div>
            </div>
        </div>

        <div id="s-multi" class="section">
            <div class="card">
                <h3><i class="fas fa-images"></i> Imágenes Principales</h3>
                <div class="media-grid">
                    <?php
                    $principales = ["1.jpg" => "Splash", "10.jpg" => "Hero", "11.jpg" => "Frase Final"];
                    foreach($principales as $img => $n): ?>
                    <div class="media-item">
                        <img src="clientes/<?php echo $nombre_usuario; ?>/<?php echo $img; ?>?v=<?php echo filemtime(__DIR__ . "/clientes/$nombre_usuario/$img"); ?>" id="preview-<?php echo str_replace('.','-',$img); ?>">
                        <label class="upload-btn">
                            Cambiar <?php echo $n; ?>
                            <input type="file" hidden onchange="upload(this, '<?php echo $img; ?>')">
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card">
                <h3><i class="fas fa-grid-2"></i> Galería (8 Fotos)</h3>
                <div class="media-grid">
                    <?php for($i=2; $i<=9; $i++): $img = $i.".jpg"; ?>
                    <div class="media-item">
			<img src="clientes/<?php echo $nombre_usuario; ?>/<?php echo $img; ?>?v=<?php echo filemtime(__DIR__ . "/clientes/$nombre_usuario/$img"); ?>" id="preview-<?php echo $i; ?>-jpg">
                        <label class="upload-btn">
                            Foto <?php echo ($i-1); ?>
                            <input type="file" hidden onchange="upload(this, '<?php echo $img; ?>')">
                        </label>
                    </div>
                    <?php endfor; ?>
                </div>
                <div style="margin-top:25px;">
                    <label style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase;">Música (cancion1.mp3)</label>
                    <input type="file" accept=".mp3" onchange="upload(this, 'cancion1.mp3')" style="margin-top:10px; width:100%;">
                </div>
            </div>
        </div>

        <div id="s-regalos" class="section">
            <div class="card">
                <h3><i class="fas fa-piggy-bank"></i> Datos para Regalos</h3>
                <div class="form-grid">
                    <div class="input-group"><label>Alias CBU</label><input type="text" name="field[alias]" value="<?php echo $config['alias']; ?>"></div>
                    <div class="input-group"><label>Titular</label><input type="text" name="field[titular]" value="<?php echo $config['titular']; ?>"></div>
                    <div class="input-group" style="grid-column: span 2;"><label>Frase Regalos</label><textarea name="field[frase_regalo]"><?php echo $config['frase_regalo']; ?></textarea></div>
                </div>
            </div>
        </div>

<div id="s-frases" class="section">
    <div class="card">
        <h3><i class="fas fa-quote-left"></i> Textos y Frases de la Invitación</h3>
        <div class="form-grid">
            <div class="input-group" style="grid-column: span 2;">
                <label>Frase Sección Regalos</label>
                <input type="text" name="field[frase_regalo]" value="<?php echo $config['frase_regalo'] ?? 'Mi mayor alegría es compartir este día contigo.'; ?>">
            </div>

            <div class="input-group" style="grid-column: span 2;">
                <label>Frase Final (Sobre la imagen)</label>
                <input type="text" name="field[frase_final]" value="<?php echo $config['frase_final'] ?? 'Tu presencia ayudará a que mi noche sea inolvidable'; ?>">
            </div>
        </div>
    </div>
</div>
        </div>

        <button type="submit" class="btn-save">GUARDAR CAMBIOS</button>
    </form>
</main>


<script>
function sh(id) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
    
    document.getElementById(id).classList.add('active');
    document.getElementById('l-' + id.split('-')[1]).classList.add('active');
    
    window.location.hash = id;
}

document.addEventListener("DOMContentLoaded", function() {
    const currentHash = window.location.hash; // Captura el #s-evento, por ejemplo
    if (currentHash) {
        const id = currentHash.replace('#', '');
        if (document.getElementById(id)) {
            sh(id); // Si el ID existe, abre esa pestaña
        }
    }
});

    function upload(input, fileName) {
        if (!input.files[0]) return;
        const fd = new FormData();
        fd.append('up_file', input.files[0]);
        fd.append('file_name', fileName);

        const btn = input.parentElement;
        const originalText = btn.innerText;
        btn.innerText = "Subiendo...";

        fetch('', { method: 'POST', body: fd })
        .then(res => res.text())
	.then(data => {
    if(data.includes("Subido")) {
        const imgId = "preview-" + fileName.replace('.', '-');
        const imgEl = document.getElementById(imgId);
        if(imgEl) {
            // Agregamos un timestamp nuevo para forzar la recarga del navegador
            imgEl.src = "clientes/<?php echo $nombre_usuario; ?>/" + fileName + "?v=" + new Date().getTime();
        }
        btn.innerHTML = '¡Listo! <i class="fas fa-check"></i>';
        setTimeout(() => { btn.innerText = originalText; }, 2000);
    }
});
    }
</script>
</body>
</html>
