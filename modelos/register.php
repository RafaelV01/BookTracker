<?php
include '../config/database.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validaciones de usuario
    if (empty($username)) {
        $errors['username'] = "El usuario es obligatorio";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "El usuario debe tener al menos 3 caracteres";
    } elseif (strlen($username) > 50) {
        $errors['username'] = "El usuario no puede exceder los 50 caracteres";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "El usuario solo puede contener letras, números y guiones bajos";
    } else {
        // Verificar si el usuario ya existe
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $errors['username'] = "Este usuario ya está registrado";
        }
    }

    // Validaciones de email
    if (empty($email)) {
        $errors['email'] = "El email es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "El formato del email no es válido";
    } elseif (strlen($email) > 100) {
        $errors['email'] = "El email no puede exceder los 100 caracteres";
    } else {
        // Verificar si el email ya existe
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = "Este email ya está registrado";
        }
    }

    // Validaciones de contraseña
    if (empty($password)) {
        $errors['password'] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "La contraseña debe tener al menos 8 caracteres";
    } elseif (strlen($password) > 72) {
        $errors['password'] = "La contraseña no puede exceder los 72 caracteres";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = "La contraseña debe contener al menos una mayúscula";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors['password'] = "La contraseña debe contener al menos una minúscula";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "La contraseña debe contener al menos un número";
    }

    // Validación de confirmación de contraseña
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Por favor confirma tu contraseña";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Las contraseñas no coinciden";
    }

    // Si no hay errores, proceder con el registro
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email, $password_hash]);
            
            // Guardar mensaje de éxito en sesión
            $_SESSION['registration_success'] = "¡Cuenta creada exitosamente! Bienvenido/a, $username.";
            $_SESSION['registered_username'] = $username;
            
            // Redirigir a la página de éxito
            header('Location: register_success.php');
            exit;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Error de duplicado
                $errors['general'] = "El usuario o email ya están registrados";
            } else {
                $errors['general'] = "Error al registrar: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - BOOKTRACKER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../vistas/styles/style.css">
</head>
<body class="login-body">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            <i class="fas fa-user-plus"></i> Registro
                        </h2>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Usuario
                                </label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" 
                                       name="username" 
                                       value="<?php echo htmlspecialchars($username ?? ''); ?>"
                                       required
                                       minlength="3"
                                       maxlength="50"
                                       pattern="[a-zA-Z0-9_]+"
                                       placeholder="Ingresa tu usuario">
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                                <?php endif; ?>
                                <div class="form-text">3-50 caracteres, solo letras, números y _</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" 
                                       class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       required
                                       maxlength="100"
                                       placeholder="Ingresa tu email">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="8"
                                       maxlength="72"
                                       placeholder="Ingresa tu contraseña">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                <?php endif; ?>
                                <div class="form-text">
                                    Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i> Confirmar Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required
                                       minlength="8"
                                       maxlength="72"
                                       placeholder="Confirma tu contraseña">
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-user-plus"></i> Registrarse
                            </button>
                        </form>
                        <p class="mt-3 text-center">
                            ¿Ya tienes cuenta? <a href="../vistas/login.php" class="fw-bold">Inicia sesión aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación del lado del cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirm_password = document.getElementById('confirm_password');

            // Validar usuario
            if (username.value.length < 3) {
                showError(username, 'El usuario debe tener al menos 3 caracteres');
                valid = false;
            } else if (username.value.length > 50) {
                showError(username, 'El usuario no puede exceder los 50 caracteres');
                valid = false;
            } else if (!/^[a-zA-Z0-9_]+$/.test(username.value)) {
                showError(username, 'Solo se permiten letras, números y guiones bajos');
                valid = false;
            } else {
                clearError(username);
            }

            // Validar email
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                showError(email, 'El formato del email no es válido');
                valid = false;
            } else if (email.value.length > 100) {
                showError(email, 'El email no puede exceder los 100 caracteres');
                valid = false;
            } else {
                clearError(email);
            }

            // Validar contraseña
            if (password.value.length < 8) {
                showError(password, 'La contraseña debe tener al menos 8 caracteres');
                valid = false;
            } else if (password.value.length > 72) {
                showError(password, 'La contraseña no puede exceder los 72 caracteres');
                valid = false;
            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password.value)) {
                showError(password, 'Debe contener mayúsculas, minúsculas y números');
                valid = false;
            } else {
                clearError(password);
            }

            // Validar confirmación
            if (confirm_password.value !== password.value) {
                showError(confirm_password, 'Las contraseñas no coinciden');
                valid = false;
            } else {
                clearError(confirm_password);
            }

            if (!valid) e.preventDefault();
        });

        // Validación en tiempo real
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const requirements = document.createElement('div');
            requirements.className = 'form-text';
            
            const hasUpper = /[A-Z]/.test(password);
            const hasLower = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasLength = password.length >= 8;
            
            let message = 'Requisitos: ';
            message += hasUpper ? '✓ Mayúsculas ' : '✗ Mayúsculas ';
            message += hasLower ? '✓ Minúsculas ' : '✗ Minúsculas ';
            message += hasNumber ? '✓ Números ' : '✗ Números ';
            message += hasLength ? '✓ 8+ caracteres' : '✗ 8+ caracteres';
            
            let existingReq = this.parentNode.querySelector('.requirement-text');
            if (existingReq) {
                existingReq.textContent = message;
            } else {
                requirements.className = 'form-text requirement-text';
                requirements.textContent = message;
                this.parentNode.appendChild(requirements);
            }
        });

        // Validación en tiempo real para mejor UX
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            if (username.length >= 3 && username.length <= 50 && /^[a-zA-Z0-9_]+$/.test(username)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });

        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length <= 100) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            if (password.length >= 8 && password.length <= 72 && /(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(password)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });

        document.getElementById('confirm_password').addEventListener('input', function() {
            const confirm = this.value;
            const password = document.getElementById('password').value;
            if (confirm === password && confirm.length > 0) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
            }
        });

        function showError(input, message) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            let errorElement = input.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains('invalid-feedback')) {
                errorElement = document.createElement('div');
                errorElement.className = 'invalid-feedback';
                input.parentNode.insertBefore(errorElement, input.nextSibling);
            }
            errorElement.textContent = message;
        }

        function clearError(input) {
            input.classList.remove('is-invalid');
            const errorElement = input.nextElementSibling;
            if (errorElement && errorElement.classList.contains('invalid-feedback')) {
                errorElement.textContent = '';
            }
        }

        // Auto-quitar alertas después de 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);

        // Focus en el primer campo al cargar la página
        document.getElementById('username').focus();
    </script>
</body>
</html>