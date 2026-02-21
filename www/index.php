<?php
$config_path = __DIR__ . '/config.json';
if (!file_exists($config_path)) {
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
        "color_fondo" => "#ffe8ff",
        "frase_dress" => "Te esperamos con tu mejor look para celebrar juntos.",
        "frase_regalo" => "Mi mayor alegría es compartir este día contigo.",
        "frase_final" => "Tu presencia ayudará a que mi noche sea inolvidable"
    ];
    file_put_contents($config_path, json_encode($default_config, JSON_PRETTY_PRINT));
}
$config = json_decode(file_get_contents($config_path), true);

$c_fondo = $config['color_fondo'] ?? '#ffe8ff';
$c_titulos = $config['color_titulos'] ?? '#ff0080';
$c_frases = $config['color_frases'] ?? '#0080c0';
$c_texto = $config['color_texto_base'] ?? '#444444';

$fecha_js = $config['fecha'] . 'T' . $config['hora'] . ':00';

function hexToRgb($hex) {
    $hex = str_replace("#", "", $hex);
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "$r, $g, $b";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HauseBot | <?php echo $config['nombre']; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Montserrat:wght@300;400;600&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg-page: <?php echo $c_fondo; ?>;
      --white: #ffffff;
      --color-titulos: <?php echo $c_titulos; ?>;
      --color-frases: <?php echo $c_frases; ?>;
      --color-base: <?php echo $c_texto; ?>;
      --accent-rgb: <?php echo hexToRgb($c_titulos); ?>;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; background: var(--bg-page); color: var(--color-base); overflow-x: hidden; scroll-behavior: smooth; }

    .reveal { opacity: 0; transform: translateY(30px); transition: 1s ease-out; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    #splash { position: fixed; inset: 0; z-index: 5000; display: flex; align-items: center; justify-content: center; flex-direction: column; text-align: center; transition: opacity 1s ease, visibility 1s; background: linear-gradient(rgba(255,255,255,.7), rgba(255,255,255,.7)), url(1.jpg?v=<?php echo time(); ?>) center/cover; }
    #splash.hidden { opacity: 0; visibility: hidden; }
    .splash-name { font-family: 'Dancing Script', cursive; font-size: clamp(4rem, 15vw, 8rem); color: var(--color-titulos); margin-bottom: 20px; }
    .btn-ingresar { padding: 12px 40px; color: var(--color-titulos); text-transform: uppercase; font-weight: 600; letter-spacing: 3px; background: transparent; border: 1.5px solid var(--color-titulos); border-radius: 50px; cursor: pointer; transition: .4s; }
    .btn-ingresar:hover { background: var(--color-titulos); color: white; }

    .hero { position: relative; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; text-align: center; background: #000; }
    .hero-bg { position: absolute; inset: 0; background: url(10.jpg?v=<?php echo time(); ?>) center/cover; opacity: .6; }
    .hero-content { z-index: 3; color: #fff; width: 90%; }
    .hero-name { font-family: 'Dancing Script', cursive; font-size: clamp(4rem, 12vw, 7rem); color: #fff; }
    .countdown-container { display: flex; justify-content: center; gap: 10px; margin-top: 20px; }
    .count-item { background: rgba(255,255,255, .15); backdrop-filter: blur(5px); padding: 10px; border-radius: 12px; min-width: 70px; border: 1px solid rgba(255,255,255, .3); }
    .count-number { font-size: 1.6rem; font-weight: 600; display: block; color: #fff; }
    .count-label { font-size: 0.7rem; text-transform: uppercase; opacity: 0.9; color: #fff; }

    section { position: relative; padding: 70px 20px; text-align: center; max-width: 800px; margin: auto; }
    .subtitle { text-transform: uppercase; letter-spacing: 4px; font-size: .85rem; color: var(--color-frases); font-weight: 600; margin-bottom: 10px; }
    .title { font-family: 'Dancing Script', cursive; font-size: clamp(2.8rem, 9vw, 3.8rem); color: var(--color-titulos); margin-bottom: 25px; }
    .premium-card { background: var(--white); border-radius: 30px; padding: 40px 30px; box-shadow: 0 15px 35px rgba(0,0,0, .06); }

    .frase-emotiva { font-family: 'Dancing Script', cursive; font-size: 2.2rem; color: var(--color-frases); line-height: 1.3; }

    /* ESTILOS FORMULARIO (SIEMPRE NEGRO) */
    label { font-size: 0.9rem; font-weight: 600; margin-bottom: 10px; display: block; color: #000000 !important; text-align: left; }
    .invitado-input, select { padding: 14px; border-radius: 12px; border: 1.5px solid #000000; width: 100%; font-family: 'Montserrat', sans-serif; background: #ffffff; color: #000000 !important; font-size: 0.95rem; -webkit-appearance: none; }
    .invitado-input::placeholder { color: #666; }
    .invitado-input:focus, select:focus { outline: none; border-color: var(--color-frases); }

    .gallery-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 20px; }
    @media (min-width: 768px) { .gallery-grid { grid-template-columns: repeat(4, 1fr); } }
    .gallery-grid img { width: 100%; aspect-ratio: 1/1; object-fit: cover; border-radius: 15px; cursor: pointer; transition: transform .3s; }

    .alias-box { background: var(--bg-page); border: 2px dashed var(--color-frases); padding: 15px 30px; border-radius: 50px; cursor: pointer; margin: 20px 0; display: inline-block; color: var(--color-base); }
    .btn-wa { background: #25D366; color: #fff; width: 100%; border: none; padding: 18px; border-radius: 50px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: .3s; box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3); }

    .final-photo-container { position: relative; width: 100%; max-width: 700px; margin: 50px auto; border-radius: 25px; overflow: hidden; background: #000; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
    .final-photo-container img { width: 100%; opacity: .75; display: block; }
    .final-text-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #fff; padding: 40px; text-align: center; }
    .final-text-overlay h2 { font-family: 'Dancing Script', cursive; font-size: clamp(2.4rem, 8vw, 3.8rem); text-shadow: 2px 2px 15px rgba(0,0,0,0.8); color: #fff !important; }

    .music-fab { position: fixed; bottom: 25px; left: 25px; width: 55px; height: 55px; background: var(--color-titulos); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; cursor: pointer; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.2); font-size: 1.2rem; }
    #lightbox { position: fixed; inset: 0; background: rgba(0,0,0, .95); display: none; align-items: center; justify-content: center; z-index: 6000; padding: 20px; }
    footer { padding: 50px 20px; background: #0a0a0a; color: #fff; text-align: center; font-size: 0.9rem; }
    footer a { color: #fff; text-decoration: underline; font-weight: bold; }
  </style>
</head>
<body>

  <div id="splash">
    <span class="subtitle"><?php echo $config['frase_hero']; ?></span>
    <h1 class="splash-name"><?php echo $config['nombre']; ?></h1>
    <button onclick="ingresar()" class="btn-ingresar">Ingresar</button>
  </div>

  <header class="hero">
    <div class="hero-bg"></div>
    <canvas id="petals-canvas" style="position:absolute; inset:0; z-index:2; pointer-events:none;"></canvas>
    <div class="hero-content">
      <p class="subtitle"><?php echo $config['frase_hero']; ?></p>
      <h1 class="hero-name"><?php echo $config['nombre']; ?></h1>
      <div class="countdown-container">
        <div class="count-item"><span class="count-number" id="days">00</span><span class="count-label">Días</span></div>
        <div class="count-item"><span class="count-number" id="hours">00</span><span class="count-label">Hs</span></div>
        <div class="count-item"><span class="count-number" id="minutes">00</span><span class="count-label">Min</span></div>
        <div class="count-item"><span class="count-number" id="seconds">00</span><span class="count-label">Seg</span></div>
      </div>
    </div>
  </header>

<section class="reveal">
    <p class="subtitle">Celebración</p>
    <h2 class="title">¿Dónde & Cuándo?</h2>
    
    <div class="premium-card">
        <div style="margin-bottom: 25px;">
            <span style="font-size:2rem">📅</span>
            <?php
                $ts = strtotime($config['fecha']);
                $dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                $meses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
                $fecha_humana = $dias[date("w", $ts)] . " " . date("d", $ts) . " de " . $meses[date("n", $ts)];
            ?>
            <p style="margin-top: 10px; font-size: 1.15rem; color: #000; font-weight: 500;">
                <b><?php echo $fecha_humana; ?></b><br>
                <?php echo $config['hora']; ?> hs
            </p>
            <p style="font-size: 0.95rem; color: #666; margin: 5px 0 0 0;">Recordá estar unos 10 minutos antes</p>
        </div>

        <div style="margin-bottom: 25px;">
            <span style="font-size:2rem">📍</span>
            <p style="margin-top: 10px; font-size: 1.15rem; color: #000; font-weight: 500;">
                <b><?php echo $config['lugar_nombre']; ?></b><br>
                <?php echo $config['lugar_direccion']; ?>
            </p>
        </div>

        <a href="<?php echo $config['lugar_mapa']; ?>" 
           target="_blank" 
           style="background: var(--color-titulos); color: white; padding: 12px 40px; border-radius: 50px; text-decoration: none; font-weight: 600; display: inline-block; transition: 0.3s;">
           Ver Mapa
        </a>
    </div>
</section>

  <section class="reveal">
    <p class="subtitle">Dress Code</p>
    <h2 class="title"><?php echo $config['dress_code']; ?></h2>
    <div class="premium-card">
      <span style="font-size:3rem">👗👔</span>
      <p style="margin-top:20px; font-weight: 500; color:#000;"><?php echo $config['frase_dress']; ?></p>
    </div>
  </section>

  <section class="reveal">
    <p class="subtitle">Asistencia</p>
    <h2 class="title">Confirmación</h2>
    <div class="premium-card">
      <div style="margin-bottom: 25px;">
        <label>Nombres de los integrantes:</label>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <?php for($i=1; $i<=6; $i++): ?>
            <input type="text" class="invitado-input" placeholder="Nombre (<?php echo $i; ?>)">
          <?php endfor; ?>
        </div>
        <p style="font-size: 0.75rem; color: #000; margin-top: 10px; text-align: left; opacity: 0.7;">* Completar solo los que asistirán.</p>
      </div>

      <div style="margin-bottom: 20px;">
        <label>¿Régimen alimentario especial?</label>
        <select id="regimen">
          <option value="No">No, ninguno</option>
          <option value="Celiaco">Celiaco</option>
          <option value="Vegetariano">Vegetariano</option>
          <option value="Vegano">Vegano</option>
          <option value="Varios (especificar)">Varios (especificar)</option>
        </select>
      </div>

      <div style="margin-bottom: 25px;">
        <label>¿Asistirán al evento?</label>
        <select id="asis">
          <option value="✅ Sí, asistiremos">✅ Sí, asistiremos</option>
          <option value="❌ No podremos ir">❌ No podremos ir</option>
        </select>
      </div>

      <button onclick="enviarRSVP()" class="btn-wa">CONFIRMAR POR WHATSAPP</button>
    </div>
  </section>

  <section class="reveal">
    <p class="subtitle">Galería</p>
    <h2 class="title">Momentos</h2>
    <div class="gallery-grid">
      <?php for($i=2; $i<=9; $i++): ?>
      <img src="<?php echo $i; ?>.jpg?v=<?php echo time(); ?>" onclick="openLightbox(this.src)">
      <?php endfor; ?>
    </div>
  </section>

  <section class="reveal">
    <p class="subtitle">Regalos</p>
    <h2 class="title">Presentes</h2>
    <div class="premium-card">
      <p class="frase-emotiva"><?php echo $config['frase_regalo']; ?></p>
      <div class="alias-box" onclick="copyAlias()">
        <span id="aliasText" style="font-weight:700; font-size:1.4rem;"><?php echo $config['alias']; ?></span>
      </div>
      <p style="margin-top:5px; color:#000;">Titular: <b><?php echo $config['titular']; ?></b></p>
      <p id="copyMessage" style="font-size:0.85rem; margin-top:10px; color:var(--color-frases); font-weight: 600;">¡Toca el alias para copiarlo!</p>
    </div>
  </section>

  <section class="reveal" style="padding: 0; max-width: 100%; margin-top: 50px;">
    <div class="final-photo-container">
        <img src="11.jpg?v=<?php echo time(); ?>" alt="Final">
        <div class="final-text-overlay">
            <h2><?php echo $config['frase_final']; ?></h2>
        </div>
    </div>
  </section>

  <footer>
    <p>Hecho con amor por <a href="https://hauserver.com" target="_blank">HAUSERVER</a></p>
  </footer>

  <div id="lightbox" onclick="this.style.display='none'"><img id="lightbox-img" style="max-width:100%; max-height:90vh; border-radius:10px;"></div>

  <div class="music-fab" onclick="toggleMusic()" id="m-btn">🎵</div>

<audio id="audio" loop preload="auto">
  <source src="cancion1.mp3" type="audio/mpeg">
  Tu navegador no soporta la reproducción de audio.
</audio>

  <script>
    const audio = document.getElementById('audio');
    const mBtn = document.getElementById('m-btn');

function ingresar() {
    // Ocultar el splash
    document.getElementById('splash').classList.add('hidden');
    
    // Intentar reproducir
    audio.load(); // Forzamos la carga del archivo
    const playPromise = audio.play();

    if (playPromise !== undefined) {
        playPromise.then(_ => {
            // Reproducción iniciada con éxito
            mBtn.innerText = '⏸';
            console.log("Música iniciada correctamente");
        }).catch(error => {
            // El navegador bloqueó el audio o el archivo no existe
            console.log("Error al reproducir: ", error);
            mBtn.innerText = '🎵'; 
        });
    }
}
    function toggleMusic() {
      if(audio.paused) { audio.play(); mBtn.innerText = '⏸'; }
      else { audio.pause(); mBtn.innerText = '🎵'; }
    }

    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').style.display = 'flex';
    }

    function copyAlias() {
      navigator.clipboard.writeText("<?php echo $config['alias']; ?>").then(() => {
        const msg = document.getElementById('copyMessage');
        msg.innerText = "¡COPIADO CON ÉXITO! ✨";
        setTimeout(() => { msg.innerText = "¡Toca el alias para copiarlo!"; }, 2500);
      });
    }

    function enviarRSVP() {
      const inputs = document.querySelectorAll('.invitado-input');
      const regimen = document.getElementById('regimen').value;
      const asistencia = document.getElementById('asis').value;
      let invitados = [];
      inputs.forEach(i => { if(i.value.trim() !== "") invitados.push("• " + i.value.trim()); });
      if (invitados.length === 0) return alert('Por favor, ingresa al menos un nombre.');

      let msg = "✨ *CONFIRMACIÓN DE ASISTENCIA* ✨%0A%0A";
      msg += "👥 *Invitados:*%0A" + invitados.join("%0A") + "%0A%0A";
      msg += "🍱 *Régimen:* " + regimen + "%0A";
      msg += "📩 *Estado:* " + asistencia;

      window.open("https://api.whatsapp.com/send?phone=<?php echo $config['whatsapp_confirmacion']; ?>&text=" + msg, '_blank');
    }

    const targetDate = new Date("<?php echo $fecha_js; ?>").getTime();
    setInterval(() => {
      const now = new Date().getTime();
      const diff = targetDate - now;
      if (diff > 0) {
        document.getElementById('days').innerText = Math.floor(diff / 86400000).toString().padStart(2, '0');
        document.getElementById('hours').innerText = Math.floor((diff % 86400000) / 3600000).toString().padStart(2, '0');
        document.getElementById('minutes').innerText = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
        document.getElementById('seconds').innerText = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
      }
    }, 1000);

    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    const canvas = document.getElementById('petals-canvas');
    const ctx = canvas.getContext('2d');
    let pts = [];
    function resize() { canvas.width = window.innerWidth; canvas.height = window.innerHeight; }
    window.addEventListener('resize', resize); resize();
    class P {
      constructor() { this.reset(); }
      reset() { this.x = Math.random()*canvas.width; this.y = -20; this.s = 2+Math.random()*3; this.v = 0.5+Math.random()*1; this.a = 0.2+Math.random()*0.3; }
      upd() { this.y += this.v; if(this.y > canvas.height) this.reset(); }
      drw() { ctx.fillStyle = `rgba(var(--accent-rgb), ${this.a})`; ctx.beginPath(); ctx.arc(this.x, this.y, this.s, 0, Math.PI*2); ctx.fill(); }
    }
    for(let i=0; i<25; i++) pts.push(new P());
    function loop() { ctx.clearRect(0,0,canvas.width,canvas.height); pts.forEach(p=>{p.upd();p.drw();}); requestAnimationFrame(loop); }
    loop();
  </script>
</body>
</html>
