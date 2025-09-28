function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.toggle-password');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = '🔒';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = '👁️';
        }
    }

    // Validación del formulario en tiempo real
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const termsAccepted = document.getElementById('terms_conditions').checked;
        const submitBtn = document.getElementById('submitBtn');
        
        // Validaciones básicas
        if (!email || !password) {
            e.preventDefault();
            alert('Por favor, completa todos los campos obligatorios.');
            return;
        }
        
        if (!termsAccepted) {
            e.preventDefault();
            alert('Debes aceptar los términos y condiciones para continuar.');
            return;
        }
        
        // Validar formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Por favor, ingresa un correo electrónico válido.');
            return;
        }
        
        // Mostrar indicador de carga
        submitBtn.textContent = 'Iniciando sesión...';
        submitBtn.disabled = true;
        document.querySelector('.login-container').classList.add('loading');
    });

    // Limpiar mensajes de error/éxito después de un tiempo
    setTimeout(function() {
        const errorMsg = document.querySelector('.error-message');
        const successMsg = document.querySelector('.success-message');
        
        if (errorMsg) {
            errorMsg.style.transition = 'opacity 0.5s';
            errorMsg.style.opacity = '0';
            setTimeout(() => errorMsg.remove(), 500);
        }
        
        if (successMsg) {
            successMsg.style.transition = 'opacity 0.5s';
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 500);
        }
    }, 5000);
    
    // Prevenir envío múltiple del formulario
    let formSubmitted = false;
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        if (formSubmitted) {
            e.preventDefault();
            return;
        }
        formSubmitted = true;
    });