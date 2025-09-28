<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contáctenos y Nuestro Equipo</title>
<link rel="stylesheet" href="../vista/css/nosotros.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-6">

<!-- Botón Regresar -->
<a href="javascript:history.back()" class="btn-gradient fixed top-6 left-6 z-50 shadow-lg">⬅ Regresar</a>

<!-- Sección Contáctenos -->
<section class="section-box max-w-5xl mx-auto mb-12">
    <h2 class="section-title">Contáctenos</h2>
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Información -->
        <div>
            <p class="mb-3"><strong>Dirección:</strong> Calle 123, Ciudad, País</p>
            <p class="mb-3"><strong>Teléfono:</strong> +57 300 123 4567</p>
            <p class="mb-3"><strong>Email:</strong> info@empresa.com</p>
            <p>Estamos disponibles para resolver tus dudas y ayudarte en lo que necesites. ¡Escríbenos!</p>
        </div>
        <!-- Formulario -->
        <div>
            <form action="#" method="post" class="flex flex-col gap-4">
                <input type="text" name="nombre" placeholder="Tu nombre" class="p-3 rounded bg-gray-800 text-white border border-gray-600 focus:border-blue-400 outline-none">
                <input type="email" name="email" placeholder="Tu correo" class="p-3 rounded bg-gray-800 text-white border border-gray-600 focus:border-blue-400 outline-none">
                <textarea name="mensaje" rows="5" placeholder="Escribe tu mensaje..." class="p-3 rounded bg-gray-800 text-white border border-gray-600 focus:border-blue-400 outline-none"></textarea>
                <button type="submit" class="btn-gradient">Enviar mensaje</button>
            </form>
        </div>
    </div>
</section>

<!-- Sección Nuestro Equipo -->
<section class="section-box max-w-5xl mx-auto">
    <h2 class="section-title">Nuestro Equipo</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="team-card">
            <img src="https://via.placeholder.com/120" alt="Miembro del equipo">
            <h3 class="text-xl font-semibold">Camilo Rivera</h3>
            <p class="text-blue-300">CEO & Fundador</p>
        </div>
        <div class="team-card">
            <img src="https://via.placeholder.com/120" alt="Miembro del equipo">
            <h3 class="text-xl font-semibold">Laura Gómez</h3>
            <p class="text-blue-300">Diseñadora UX/UI</p>
        </div>
        <div class="team-card">
            <img src="https://via.placeholder.com/120" alt="Miembro del equipo">
            <h3 class="text-xl font-semibold">Pedro Torres</h3>
            <p class="text-blue-300">Desarrollador Full Stack</p>
        </div>
    </div>
</section>

</body>
</html>
