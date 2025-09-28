<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/contactenos.css">
    <title>Joystick genius | Conexión Gamer Futurista</title>
</head>
<body>
    <div class="fondo-animado"></div>
    
    <a href="javascript:history.back()" class="boton-retro">← Volver al Nexo</a>

    <div class="container">
        <header class="panel-futurista">
            <h1>Portal de Conexión Gamer</h1>
            <p>Transmite tu señal digital. Cada mensaje es un protocolo de conexión con Pixel Play.</p>
        </header>

        <div class="contenido-contacto">
            <div class="texto-presentacion panel-futurista">
                <h2 style="color: var(--acento-cyan); margin-bottom: 1rem;">Conecta con Pixel Play</h2>
                <p>Bienvenido al portal de comunicación de Pixel Play, tu punto de encuentro digital. Aquí puedes:</p>
                <ul style="list-style-type: none; padding-left: 0; margin-top: 1rem;">
                    <li style="margin-bottom: 0.5rem;">🎮 Compartir ideas de juego</li>
                    <li style="margin-bottom: 0.5rem;">🤝 Solicitar soporte</li>
                    <li style="margin-bottom: 0.5rem;">💡 Proponer colaboraciones</li>
                    <li style="margin-bottom: 0.5rem;">📡 Establecer comunicación directa</li>
                </ul>
                <p style="margin-top: 1rem;">Nuestro equipo está listo para recibir tu transmisión.</p>
            </div>

            <div class="panel-futurista">
                <form class="formulario-contacto" action="https://formsubmit.co/carmona167803@gmail.com" method="POST">
                    <div class="grupo-input">
                        <label for="nombre">Gamertag | Identidad Digital</label>
                        <input type="text" id="name" name="name" placeholder="Introduce tu identidad virtual" required>
                    </div>
                    
                    <div class="grupo-input">
                        <label for="email">Coordenadas de Transmisión</label>
                        <input type="email" id="email" name="email" placeholder="Canal de comunicación electrónica" required>
                    </div>
                    
                    <div class="grupo-input">
                        <label for="mensaje">Protocolo de Mensaje</label>
                        <textarea id="messaje" name="messaje" rows="4" placeholder="Descarga tu mensaje en nuestro sistema..." required></textarea>
                    </div>
                    
                    <button type="submit" class="boton-enviar">Enviar Transmisión</button>

                    <input type="hidden" name="_next" value="http://localhost/joystick%20genius/controlador/contactenos.php?">
                    <input type="hidden" value="_captcha" value="false">
                </form>
            </div>
        </div>

        <div class="contenido-contacto" style="margin-top: 2rem;">
            <div class="info-contacto panel-futurista">
                <h2 style="color: var(--acento-cyan); margin-bottom: 1rem;">Canales de Comunicación</h2>
                <p>📡 <strong>Frecuencia de Soporte:</strong> carmona167803@gmail.com</p>
                <p>🌐 <strong>Línea de Conexión:</strong> +57 321 52 56 034</p>
            </div>

            <div class="enlaces-sociales panel-futurista">
                <h2 style="color: var(--acento-cyan); margin-bottom: 1rem;">Redes de Conexión</h2>
                <h2>no displonibles</h2>
                <a href="#" class="enlace-social">
                    <img src="/multimedia/icons/twitter.png" alt="Twitter" width="30" height="30" style="margin-right: 10px;"> Twitter
                </a>
                <a href="#" class="enlace-social">
                    <img src="/multimedia/icons/linkedin.png" alt="LinkedIn" width="30" height="30" style="margin-right: 10px;"> LinkedIn
                </a>
                <a href="#" class="enlace-social">
                    <img src="/multimedia/icons/instagram.png" alt="Instagram" width="30" height="30" style="margin-right: 10px;"> Instagram
                </a>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2024 joystick genius | Red de Comunicación Gamer Futurista</p>
    </footer>
</body>
</html>