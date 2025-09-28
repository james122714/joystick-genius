<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joystick Genius</title>
    <link rel="icon" href="../vista/multimedia/logo.jpeg" type="image/x-icon">
    <link rel="stylesheet" href="../vista/css/iniciopag.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <script>
        // Simple particles simulation
        function createParticles() {
            const particles = document.getElementById('particles');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.style.position = 'absolute';
                particle.style.width = '2px';
                particle.style.height = '2px';
                particle.style.background = 'var(--accent-color)';
                particle.style.borderRadius = '50%';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animation = `float ${Math.random() * 3 + 2}s infinite`;
                particles.appendChild(particle);
            }
        }
        window.onload = createParticles;

        // Mobile menu toggle
        function toggleMobileMenu() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('hidden');
        }
    </script>
</head>
<body>
    <div class="particles" id="particles"></div>
    <div class="floating-elements" id="floatingElements"></div>

    <header class="glass p-4 sticky top-0 z-50">
        <nav class="nav container mx-auto flex justify-between items-center">
            <div class="logo text-3xl font-bold neon-text">Joystick Genius</div>
            <ul class="nav-links hidden md:flex space-x-8">
                <li><a href="comunidad.php" class="text-white hover:text-accent-color transition-colors">Comunidad</a></li>
                <li><a href="noticias.php" class="text-white hover:text-accent-color transition-colors">Noticias</a></li>
                <li><a href="servicios.php" class="text-white hover:text-accent-color transition-colors">Servicios</a></li>
                <li><a href="nosotros.php" class="text-white hover:text-accent-color transition-colors">Nosotros</a></li>
            </ul>
            <div class="auth-buttons">
                <a href="login.php" class="btn-login">Iniciar Sesión</a>
                <a href="registro.php" class="btn-register">Registrarse</a>
            </div>
            <button class="mobile-menu md:hidden text-white" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main class="main-content">
        <section class="hero" id="inicio">
            <div class="container mx-auto px-4 text-center relative z-10">
                <h1 class="text-5xl md:text-6xl font-bold mb-6 neon-text">¡Bienvenido a Joystick Genius!</h1>
                <p class="text-xl md:text-2xl mb-8 text-white max-w-3xl mx-auto leading-relaxed">
                    El portal gaming más completo donde conectamos jugadores de todo el mundo. Descubre, juega y comparte tu pasión por los videojuegos.
                </p>
                <button class="cta-button futuristic-btn" onclick="window.location.href='login.php'">
                    ¡Únete Ahora! 🚀
                </button>
            </div>
        </section>

        <section class="welcome-message py-20 bg-black/20">
            <div class="container mx-auto px-4 text-center">
                <h2 class="section-title mb-6">🎯 ¿Qué es GameZone?</h2>
                <p class="text-lg text-secondary max-w-4xl mx-auto leading-relaxed">
                    GameZone es tu comunidad gaming definitiva. Un lugar donde los videojuegos cobran vida a través de una experiencia única que combina entretenimiento, comunidad y tecnología de vanguardia.
                </p>
            </div>
        </section>

        <section class="stats-grid py-20">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="stat-card glass rounded-2xl p-8 text-center neon-border">
                        <div class="stat-number text-4xl font-bold text-accent-color mb-4">500K+</div>
                        <p class="text-secondary">Jugadores Registrados</p>
                    </div>
                    <div class="stat-card glass rounded-2xl p-8 text-center neon-border">
                        <div class="stat-number text-4xl font-bold text-accent-color mb-4">10K+</div>
                        <p class="text-secondary">Contenidos Disponibles</p>
                    </div>
                    <div class="stat-card glass rounded-2xl p-8 text-center neon-border">
                        <div class="stat-number text-4xl font-bold text-accent-color mb-4">24/7</div>
                        <p class="text-secondary">Comunidad Activa</p>
                    </div>
                    <div class="stat-card glass rounded-2xl p-8 text-center neon-border">
                        <div class="stat-number text-4xl font-bold text-accent-color mb-4">50+</div>
                        <p class="text-secondary">Países Conectados</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="features-grid py-20 bg-black/20">
            <div class="container mx-auto px-4">
                <h2 class="section-title mb-12">✨ Características que Te Harán Quedarte</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="feature-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="feature-icon fas fa-users text-4xl text-accent-color mb-4"></i>
                        <h3 class="text-2xl font-bold mb-4 text-white">Comunidad Vibrante</h3>
                        <p class="text-secondary leading-relaxed">
                            Conecta con jugadores de todo el mundo. Comparte experiencias, forma equipos y haz nuevos amigos que comparten tu pasión por los videojuegos.
                        </p>
                    </div>
                    <div class="feature-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="feature-icon fas fa-newspaper text-4xl text-accent-color mb-4"></i>
                        <h3 class="text-2xl font-bold mb-4 text-white">Noticias y Reviews</h3>
                        <p class="text-secondary leading-relaxed">
                            Mantente informado con las últimas noticias del gaming. Reviews exclusivas, análisis profundos y todo lo que necesitas saber del mundo gamer.
                        </p>
                    </div>
                    <div class="feature-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="feature-icon fas fa-video text-4xl text-accent-color mb-4"></i>
                        <h3 class="text-2xl font-bold mb-4 text-white">Contenido Multimedia</h3>
                        <p class="text-secondary leading-relaxed">
                            Disfruta de videos, streams, tutoriales y contenido exclusivo creado por nuestra comunidad de gamers apasionados.
                        </p>
                    </div>
                    <div class="feature-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="feature-icon fas fa-trophy text-4xl text-accent-color mb-4"></i>
                        <h3 class="text-2xl font-bold mb-4 text-white">Eventos y Torneos</h3>
                        <p class="text-secondary leading-relaxed">
                            Participa en emocionantes torneos y eventos especiales. Demuestra tus habilidades y gana increíbles premios.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="info-section py-20" id="servicios">
            <div class="container mx-auto px-4">
                <h2 class="section-title">🌟 Nuestros Servicios</h2>
                <div class="info-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="info-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="info-icon fas fa-headset text-4xl text-accent-color mb-4"></i>
                        <h4 class="text-2xl font-bold mb-4 text-white">Soporte 24/7</h4>
                        <p class="text-secondary leading-relaxed">
                            Equipo de soporte disponible las 24 horas para ayudarte con cualquier consulta o problema técnico.
                        </p>
                    </div>
                    <div class="info-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="info-icon fas fa-shield-alt text-4xl text-accent-color mb-4"></i>
                        <h4 class="text-2xl font-bold mb-4 text-white">Seguridad Avanzada</h4>
                        <p class="text-secondary leading-relaxed">
                            Protección de datos de última generación para mantener tu información y experiencia gaming segura.
                        </p>
                    </div>
                    <div class="info-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="info-icon fas fa-mobile-alt text-4xl text-accent-color mb-4"></i>
                        <h4 class="text-2xl font-bold mb-4 text-white">Multiplataforma</h4>
                        <p class="text-secondary leading-relaxed">
                            Accede desde cualquier dispositivo. PC, móvil, tablet - GameZone está contigo donde vayas.
                        </p>
                    </div>
                    <div class="info-card glass rounded-2xl p-8 text-center neon-border">
                        <i class="info-icon fas fa-star text-4xl text-accent-color mb-4"></i>
                        <h4 class="text-2xl font-bold mb-4 text-white">Experiencia Premium</h4>
                        <p class="text-secondary leading-relaxed">
                            Interfaz intuitiva, rendimiento optimizado y características exclusivas para una experiencia gaming superior.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="social-section py-20 bg-black/20" id="comunidad">
            <div class="container mx-auto px-4 text-center">
                <h2 class="section-title mb-6" style="color: #ff3333; font-size: 3rem;">🌐 Únete a Nuestra Comunidad</h2>
                <p class="text-lg text-secondary mb-8 max-w-3xl mx-auto leading-relaxed">
                    Síguenos en redes sociales y mantente conectado con la comunidad gaming más activa
                </p>
                <div class="social-links flex justify-center">
                    <a href="#" class="social-link" title="Discord"><i class="fab fa-discord"></i></a>
                    <a href="#" class="social-link" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-link" title="Twitch"><i class="fab fa-twitch"></i></a>
                    <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link" title="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer py-12 bg-black/60 border-t border-red-500/30">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
                <div>
                    <h3 class="text-2xl font-bold text-red-300 mb-4">🎮 GameZone - Tu Portal Gaming</h3>
                    <p class="text-secondary mb-4">Conectando gamers desde 2024. Una comunidad apasionada por los videojuegos.</p>
                    <p class="text-sm text-gray-400">&copy; 2024 GameZone. Todos los derechos reservados. | Hecho con ❤️ para gamers</p>
                </div>
                <div>
                    <h4 class="text-lg text-red-200 mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-secondary">
                        <li><a href="index.php" class="hover:text-accent-color">🏠 Inicio</a></li>
                        <li><a href="comunidad.php" class="hover:text-accent-color">👥 Comunidad</a></li>
                        <li><a href="noticias.php" class="hover:text-accent-color">📰 Noticias</a></li>
                        <li><a href="servicios.php" class="hover:text-accent-color">⚙️ Servicios</a></li>
                        <li><a href="nosotros.php" class="hover:text-accent-color">👥 Nosotros</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg text-red-200 mb-4">Contacto</h4>
                    <p class="text-secondary mb-2">📧 info@gamezone.com</p>
                    <p class="text-secondary mb-2">📱 WhatsApp: +57 300 123 4567</p>
                    <p class="text-secondary">📍 Medellín, Colombia</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>