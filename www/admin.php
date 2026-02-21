<?php
$config_path = __DIR__ . '/config.json';
$config = json_decode(file_get_contents($config_path), true);
$msg = "";

// Procesar cambios de texto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_config'])) {
    foreach ($_POST['field'] as $key => $value) { $config[$key] = $value; }
    file_put_contents($config_path, json_encode($config, JSON_PRETTY_PRINT));
    $msg = "Configuración guardada correctamente.";
}

// Procesar subida de archivos (Imágenes y MP3)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['up_file'])) {
    $dest = $_POST['file_name'];
    if(move_uploaded_file($_FILES['up_file']['tmp_name'], __DIR__ . '/' . $dest)) {
        echo "success"; // Respuesta para el fetch JS
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Premium | Tarjetas Digitales</title>
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

        /* SIDEBAR */
        nav { width: 280px; background: var(--sidebar-dark); padding: 40px 20px; border-right: 1px solid var(--border); position: fixed; height: 100vh; z-index: 100; }
        .logo-area { display: flex; align-items: center; gap: 12px; margin-bottom: 50px; padding: 0 10px; }
        .logo-area i { color: var(--primary); font-size: 1.5rem; }
        .logo-area h2 { font-size: 1.1rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        
        nav a { 
            color: var(--text-muted); text-decoration: none; padding: 14px 18px; border-radius: 12px; 
            display: flex; align-items: center; gap: 12px; font-weight: 500; transition: 0.3s; margin-bottom: 5px; cursor: pointer;
        }
        nav a:hover, nav a.active { background: rgba(255, 255, 255, 0.05); color: #fff; }
        nav a.active { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(255, 0, 128, 0.3); }

        /* CONTENT */
        main { flex: 1; margin-left: 280px; padding: 50px; max-width: 1100px; }
        .card { background: var(--card-bg); border-radius: 20px; padding: 30px; border: 1px solid var(--border); margin-bottom: 30px; animation: fadeIn 0.4s ease; }
        .card h3 { font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .input-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        .input-group label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        
        input, select, textarea { background: var(--input-bg); border: 1px solid var(--border); padding: 12px; border-radius: 10px; color: white; font-family: inherit; }
        input[type="color"] { height: 50px; padding: 5px; cursor: pointer; background: none; border: 1px solid var(--border); }

        /* MULTIMEDIA GRID */
        .media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px; }
        .media-item { background: var(--input-bg); border-radius: 15px; padding: 10px; border: 1px solid var(--border); text-align: center; position: relative; }
        .media-item img { width: 100%; height: 120px; object-fit: cover; border-radius: 10px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.1); }
        .upload-btn { background: rgba(255,255,255,0.05); border: 1px dashed var(--border); padding: 8px; border-radius: 8px; font-size: 0.7rem; cursor: pointer; display: block; transition: 0.3s; }
        .upload-btn:hover { border-color: var(--primary); color: var(--primary); }

        .btn-save { background: var(--primary); color: white; border: none; padding: 20px; border-radius: 15px; font-weight: 700; cursor: pointer; width: 100%; font-size: 1rem; box-shadow: 0 4px 15px rgba(255, 0, 128, 0.2); transition: 0.3s; }
        .btn-save:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(255, 0, 128, 0.4); }

        .section { display: none; }
        .section.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav>
    <div class="logo-area"><i class="fas fa-robot"></i><h2><span>Panel de control</span></h2></div>
    <a onclick="sh('s-info')" id="l-info" class="active"><i class="fas fa-id-card"></i> Información</a>
    <a onclick="sh('s-evento')" id="l-evento"><i class="fas fa-map-marker-alt"></i> Evento</a>
    <a onclick="sh('s-multi')" id="l-multi"><i class="fas fa-photo-film"></i> Multimedia</a>
    <a onclick="sh('s-regalos')" id="l-regalos"><i class="fas fa-gift"></i> Regalos</a>
    <a onclick="sh('s-frases')" id="l-frases"><i class="fas fa-quote-right"></i> Frases</a>
    <div style="margin-top: auto;"><a href="index.php" target="_blank" style="color:var(--primary)"><i class="fas fa-laptop"></i> Ver Sitio</a></div>
</nav>

<main>
    <?php if($msg): ?> <div style="background: rgba(16,185,129,0.1); border: 1px solid #10b981; color:#10b981; padding:15px; border-radius:12px; margin-bottom:20px;"><?php echo $msg; ?></div> <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="update_config" value="1">

        <div id="s-info" class="section active">
            <div class="card">
                <h3><i class="fas fa-palette"></i> Colores Premium</h3>
                <div class="form-grid">
                    <div class="input-group"><label>Subtitulos</label><input type="color" name="field[color_titulos]" value="<?php echo $config['color_titulos']; ?>"></div>
                    <div class="input-group"><label>Titulo</label><input type="color" name="field[color_frases]" value="<?php echo $config['color_frases']; ?>"></div>
                    <div class="input-group"><label>Texto Base</label><input type="color" name="field[color_texto_base]" value="<?php echo $config['color_texto_base']; ?>"></div>
                    <div class="input-group"><label>Fondo Web</label><input type="color" name="field[color_fondo]" value="<?php echo $config['color_fondo']; ?>"></div>
                </div>
            </div>
            <div class="card">
                <h3><i class="fas fa-user"></i> Datos Básicos</h3>
                <div class="form-grid">
                    <div class="input-group"><label>Nombre</label><input type="text" name="field[nombre]" value="<?php echo $config['nombre']; ?>"></div>
                    <div class="input-group"><label>Frase de Invitacion</label><input type="text" name="field[frase_hero]" value="<?php echo $config['frase_hero']; ?>"></div>
                    <div class="input-group"><label>WhatsApp</label><input type="text" name="field[whatsapp_confirmacion]" value="<?php echo $config['whatsapp_confirmacion']; ?>"></div>
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
                        <img src="<?php echo $img; ?>?v=<?php echo time(); ?>" id="preview-<?php echo str_replace('.','-',$img); ?>">
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
                        <img src="<?php echo $img; ?>?v=<?php echo time(); ?>" id="preview-<?php echo $i; ?>-jpg">
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
                <h3><i class="fas fa-comment-dots"></i> Frases Finales</h3>
                <div class="input-group"><label>Dress Code (Título)</label><input type="text" name="field[dress_code]" value="<?php echo $config['dress_code']; ?>"></div>
                <div class="input-group"><label>Frase Dress Code</label><input type="text" name="field[frase_dress]" value="<?php echo $config['frase_dress']; ?>"></div>
                <div class="input-group" style="margin-top:15px;"><label>Frase Final del Sitio</label><textarea name="field[frase_final]" rows="4"><?php echo $config['frase_final']; ?></textarea></div>
            </div>
        </div>

        <button type="submit" class="btn-save">GUARDAR TODOS LOS TEXTOS</button>
    </form>
</main>

<script>
    function sh(id) {
        document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('nav a').forEach(a => a.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        document.getElementById('l-' + id.split('-')[1]).classList.add('active');
    }

    function upload(input, fileName) {
        if (!input.files[0]) return;
        const fd = new FormData();
        fd.append('up_file', input.files[0]);
        fd.append('file_name', fileName);
        
        const btn = input.parentElement;
        btn.innerText = "Subiendo...";

        fetch('', { method: 'POST', body: fd })
        .then(res => res.text())
        .then(data => {
            if(data.includes("success")) {
                const imgId = "preview-" + fileName.replace('.', '-');
                const imgEl = document.getElementById(imgId);
                if(imgEl) imgEl.src = fileName + "?v=" + new Date().getTime();
                btn.innerHTML = '¡Listo! <i class="fas fa-check"></i>';
                setTimeout(() => { btn.innerText = "Cambiar archivo"; }, 2000);
            }
        });
    }
</script>
</body>
</html>
