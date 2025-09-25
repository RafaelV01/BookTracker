<?php
include 'config/database.php';
session_start();

// Mostrar mensaje de éxito si viene de un registro exitoso
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

// Inicializar variables de error
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones
    if (empty($username)) {
        $errors['username'] = "El usuario es obligatorio";
    } elseif (strlen($username) < 3) {
        $errors['username'] = "El usuario debe tener al menos 3 caracteres";
    } elseif (strlen($username) > 50) {
        $errors['username'] = "El usuario no puede exceder los 50 caracteres";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors['username'] = "El usuario solo puede contener letras, números y guiones bajos";
    }

    if (empty($password)) {
        $errors['password'] = "La contraseña es obligatoria";
    } elseif (strlen($password) < 8) { // Cambiado a 8 caracteres mínimo
        $errors['password'] = "La contraseña debe tener al menos 8 caracteres";
    } elseif (strlen($password) > 72) {
        $errors['password'] = "La contraseña no puede exceder los 72 caracteres";
    }

    // Solo verificar en la base de datos si no hay errores
    if (empty($errors)) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_success'] = "¡Bienvenido de nuevo, {$user['username']}!";
            header('Location: dashboard.php');
            exit;
        } else {
            $errors['general'] = "Usuario o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - BOOKTRACKER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Iniciar Sesión</h2>
                        
                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                        <?php endif; ?>

                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" 
                                       class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" 
                                       id="username" 
                                       name="username" 
                                       value="<?php echo htmlspecialchars($username ?? ''); ?>"
                                       required
                                       minlength="3"
                                       maxlength="50"
                                       pattern="[a-zA-Z0-9_]+">
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['username']; ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" 
                                       class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" 
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="8"
                                       maxlength="72">
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                <?php endif; ?>
                                <div class="form-text">La contraseña debe tener al menos 8 caracteres.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                        </form>
                        <p class="mt-3 text-center">¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validación del lado del cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            let valid = true;
            const username = document.getElementById('username');
            const password = document.getElementById('password');

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

            // Validar contraseña
            if (password.value.length < 8) {
                showError(password, 'La contraseña debe tener al menos 8 caracteres');
                valid = false;
            } else if (password.value.length > 72) {
                showError(password, 'La contraseña no puede exceder los 72 caracteres');
                valid = false;
            } else {
                clearError(password);
            }

            if (!valid) e.preventDefault();
        });

        function showError(input, message) {
            input.classList.add('is-invalid');
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
                errorElement.remove();
            }
        }
    </script>
</body>
</html>