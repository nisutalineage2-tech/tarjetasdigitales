#!/bin/bash

# Configuración extraída de tu docker-compose
CONTAINER_DB="tarjetas_db"
MYSQL_USER="root"
MYSQL_PASS="dIxb31NvfH6li4Qc"
MYSQL_DB="tarjetas_db"

# Rutas locales
BASE_WWW="./www"
CLIENTES_DIR="$BASE_WWW/clientes"

# Colores
BLUE='\033[0;34m'
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

# Validar argumento
if [ -z "$1" ]; then
    echo -e "${BLUE}Usage:${NC} ./crear_cliente.sh nombre_usuario"
    exit 1
fi

USER=$1
# Generamos el hash de '123456' usando el contenedor de PHP (tarjetas)
# Esto asegura compatibilidad total de versiones
HASH=$(docker exec tarjetas php -r "echo password_hash('123456', PASSWORD_DEFAULT);")

echo -e "${BLUE}Procesando cliente:${NC} $USER..."

# Definimos el JSON dinámico
CONFIG_JSON=$(cat <<EOF
{
    "nombre": "$USER",
    "frase_hero": "Mis 15 Años",
    "fecha": "2026-04-03",
    "hora": "21:00",
    "lugar_nombre": "Salón Elegance",
    "lugar_direccion": "Av. Siempre Viva 742, CABA",
    "lugar_mapa": "https://goo.gl/maps/xyz",
    "color_fondo": "#fdf2f8",
    "color_titulos": "#db2777",
    "color_frases": "#9d174d",
    "color_texto_base": "#333333",
    "dress_code": "Elegante Sport",
    "whatsapp_confirmacion": "5491122334455",
    "alias": "${USER^^}.15.FIESTA",
    "titular": "$USER Sosa",
    "frase_regalo": "Tu presencia es mi mejor regalo...",
    "frase_final": "¡Te espero para brindar!"
}
EOF
)

# Ejecutar Query DENTRO del contenedor de MySQL
docker exec -i $CONTAINER_DB mysql -u$MYSQL_USER -p$MYSQL_PASS $MYSQL_DB <<EOF
DELETE FROM usuarios WHERE usuario = '$USER';
INSERT INTO usuarios (usuario, password, activo, config_json) 
VALUES ('$USER', '$HASH', 1, '$CONFIG_JSON');
EOF

if [ $? -eq 0 ]; then
    # Crear carpeta física y clonar archivos base de cliente1
    TARGET_DIR="$CLIENTES_DIR/$USER"
    mkdir -p "$TARGET_DIR"
    
    if [ -d "$CLIENTES_DIR/cliente1" ]; then
        cp -r "$CLIENTES_DIR/cliente1/"* "$TARGET_DIR/"
        echo -e "${GREEN}✔ Archivos clonados de cliente1${NC}"
    fi

    # Ajustar permisos para que el contenedor pueda leer/escribir
    chmod -R 755 "$TARGET_DIR"
    
    echo -e "${GREEN}------------------------------------------${NC}"
    echo -e "✅ Cliente ${GREEN}$USER${NC} creado con éxito."
    echo -e "🔑 Password: 123456"
    echo -e "📁 Carpeta: $TARGET_DIR"
    echo -e "🌐 URL: http://localhost:8082/index.php?u=$USER"
    echo -e "${GREEN}------------------------------------------${NC}"
else
    echo -e "${RED}❌ Error al insertar en la base de datos.${NC}"
fi
