document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario de registro
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            let isValid = true;
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            // Validar contraseña
            if (password.length < 8) {
                showError('password', 'La contraseña debe tener al menos 8 caracteres');
                isValid = false;
            } else if (!/[A-Z]/.test(password)) {
                showError('password', 'La contraseña debe contener al menos una mayúscula');
                isValid = false;
            } else if (!/[0-9]/.test(password)) {
                showError('password', 'La contraseña debe contener al menos un número');
                isValid = false;
            }

            // Validar confirmación de contraseña
            if (password !== passwordConfirm) {
                showError('password_confirmation', 'Las contraseñas no coinciden');
                isValid = false;
            }

            // Validar email
            const email = document.getElementById('email').value;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError('email', 'Por favor ingrese un correo electrónico válido');
                isValid = false;
            }

            // Validar nombre
            const name = document.getElementById('name').value;
            if (name.trim().length < 3) {
                showError('name', 'El nombre debe tener al menos 3 caracteres');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Validación del formulario de login
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showError('email', 'Por favor ingrese un correo electrónico válido');
                isValid = false;
            }

            if (password.trim() === '') {
                showError('password', 'La contraseña es requerida');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

function showError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    // Remover mensaje de error anterior si existe
    const existingError = field.nextElementSibling;
    if (existingError && existingError.className === 'invalid-feedback') {
        existingError.remove();
    }
    
    field.classList.add('is-invalid');
    field.parentNode.appendChild(errorDiv);
}