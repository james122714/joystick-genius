<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>GameZone - Servicios</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/servicios.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="particles" id="particles"></div>

    <header class="header">
        <nav class="nav">
            <div class="logo">🎮 geniusZone</div>
            <ul class="nav-links">
                <li><a href="pagina_inicio.php">Inicio</a></li>
                <li><a href="servicios.php" class="active">Servicios</a></li>
                <li><a href="comunidad.php">Comunidad</a></li>
                <li><a href="noticias.php">Noticias</a></li>
                <li><a href="contactenos.php">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <section class="page-header">
            <h1 class="page-title">🔧 Nuestros Servicios</h1>
            <p class="page-subtitle">Descubre todas las increíbles funcionalidades que GameZone tiene preparadas para ti</p>
        </section>

        <div class="services-grid">
            <div class="service-card">
                <div class="status-badge">Disponible</div>
                <i class="service-icon fas fa-download"></i>
                <h3 class="service-title">Centro de Descargas</h3>
                <p class="service-description">Accede a nuestra biblioteca completa de contenido gaming con sistema de búsqueda avanzado y categorización inteligente.</p>
                <ul class="service-features">
                    <li>Búsqueda avanzada por género</li>
                    <li>Filtros por año y plataforma</li>
                    <li>Sistema de recomendaciones</li>
                    <li>Historial de descargas</li>
                </ul>
                <div class="service-price">Gratis</div>
                <button class="service-btn">🚀 Explorar Ahora</button>
            </div>

            <div class="service-card">
                <div class="status-badge">Disponible</div>
                <i class="service-icon fas fa-newspaper"></i>
                <h3 class="service-title">Portal de Noticias</h3>
                <p class="service-description">Mantente al día con las últimas noticias, reviews y análisis del mundo gaming con contenido actualizado diariamente.</p>
                <ul class="service-features">
                    <li>Noticias actualizadas 24/7</li>
                    <li>Reviews exclusivas</li>
                    <li>Análisis de expertos</li>
                    <li>Alertas personalizadas</li>
                </ul>
                <div class="service-price">Gratis</div>
                <button class="service-btn">📰 Ver Noticias</button>
            </div>

            <div class="service-card">
                <div class="status-badge">Fuera de Servicio</div>
                <i class="service-icon fas fa-video"></i>
                <h3 class="service-title">Streaming & Videos</h3>
                <p class="service-description">Disfruta de streams en vivo, gameplays exclusivos y contenido multimedia de alta calidad.</p>
                <ul class="service-features">
                    <li>Streams en vivo HD</li>
                    <li>Gameplays exclusivos</li>
                    <li>Tutoriales especializados</li>
                    <li>Contenido bajo demanda</li>
                </ul>
                <div class="service-price">Próximamente</div>
                <button class="service-btn" disabled>🔜 Muy Pronto</button>
            </div>

            <div class="service-card">
                <div class="status-badge">Fuera de Servicio</div>
                <i class="service-icon fas fa-crown"></i>
                <h3 class="service-title">Membresía VIP</h3>
                <p class="service-description">Acceso premium con beneficios exclusivos, contenido anticipado y experiencia sin publicidad.</p>
                <ul class="service-features">
                    <li>Acceso anticipado a contenido</li>
                    <li>Velocidad de descarga premium</li>
                    <li>Soporte prioritario 24/7</li>
                    <li>Contenido exclusivo VIP</li>
                </ul>
                <div class="service-price">$9.99/mes</div>
                <button class="service-btn" disabled>👑 En Desarrollo</button>
            </div>

            <div class="service-card">
                <div class="status-badge">Fuera de Servicio</div>
                <i class="service-icon fas fa-trophy"></i>
                <h3 class="service-title">Torneos & Eventos</h3>
                <p class="service-description">Participa en emocionantes torneos, eventos especiales y competencias con premios increíbles.</p>
                <ul class="service-features">
                    <li>Torneos semanales</li>
                    <li>Eventos especiales</li>
                    <li>Sistema de ranking</li>
                    <li>Premios y reconocimientos</li>
                </ul>
                <div class="service-price">Gratis</div>
                <button class="service-btn" disabled>🏆 Próximamente</button>
            </div>

            <div class="service-card">
                <div class="status-badge">Fuera de Servicio</div>
                <i class="service-icon fas fa-comments"></i>
                <h3 class="service-title">Chat & Foros</h3>
                <p class="service-description">Conecta con otros gamers a través de nuestro sistema de chat avanzado y foros especializados.</p>
                <ul class="service-features">
                    <li>Chat en tiempo real</li>
                    <li>Foros por categorías</li>
                    <li>Mensajería privada</li>
                    <li>Grupos especializados</li>
                </ul>
                <div class="service-price">Gratis</div>
                <button class="service-btn" disabled>💬 En Construcción</button>
            </div>
        </div>

        <section class="coming-soon">
            <h2 style="font-size: 2.5rem; margin-bottom: 1rem; color: #ffaa00;">🚧 Más Servicios en Camino</h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem;">Estamos trabajando arduamente para traerte nuevas funcionalidades increíbles</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 2rem;">
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 15px;">
                    <i class="fas fa-gamepad" style="font-size: 2rem; color: #ff6b6b; margin-bottom: 0.5rem;"></i>
                    <p>Biblioteca Personal</p>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 15px;">
                    <i class="fas fa-cloud" style="font-size: 2rem; color: #4ecdc4; margin-bottom: 0.5rem;"></i>
                    <p>Guardado en la Nube</p>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 15px;">
                    <i class="fas fa-mobile-alt" style="font-size: 2rem; color: #ffaa00; margin-bottom: 0.5rem;"></i>
                    <p>App Móvil</p>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 15px;">
                    <i class="fas fa-brain" style="font-size: 2rem; color: #ff00ff; margin-bottom: 0.5rem;"></i>
                    <p>IA Recomendaciones</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3>🎮 GameZone - Tu Portal Gaming</h3>
            <p>Conectando gamers desde 2024. Una comunidad apasionada por los videojuegos.</p>
            <div style="margin: 2rem 0;">
                <p>📧 Contacto: info@gamezone.com</p>
                <p>📱 WhatsApp: +57 300 123 4567</p>
                <p>📍 Medellín, Colombia</p>
            </div>
            <p>&copy; 2024 joystick genius. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Crear partículas
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * window.innerWidth + 'px';
            particle.style.animationDelay = Math.random() * 6 + 's';
            document.getElementById('particles').appendChild(particle);

            setTimeout(() => {
                particle.remove();
            }, 6000);
        }

        // Generar partículas
        setInterval(createParticle, 500);

        // Animaciones de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.service-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                card.style.transition = `all 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });
        });
    </script>
</body>
</html>