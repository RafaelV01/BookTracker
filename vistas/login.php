<?php
include '../config/database.php';
session_start();

// Mostrar mensaje de éxito si viene de un registro exitoso
if (isset($_SESSION['registration_success'])) {
    $success_message = $_SESSION['registration_success'];
    unset($_SESSION['registration_success']);
}

// Inicializar variables de error
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas de campos no vacíos
    if (empty($username) || empty($password)) {
        $error = "Por favor, completa todos los campos";
    } else {
        // Verificar en la base de datos si el usuario existe y la contraseña es correcta
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_success'] = "¡Bienvenido de nuevo, {$user['username']}!";
            header('Location: ../modelos/dashboard.php');
            exit;
        } else {
            $error = "Usuario no registrado o contraseña incorrecta";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../vistas/styles/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #8B5E3C 0%, #F5E9DA 100%);
            min-height: 100vh;
            font-family: 'Merriweather', serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        /* Card */
        .card {
            background: #fffdfbff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            border: none;
            padding: 2rem;
        }

        /* Card Title */
        .card h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #8B5E3C;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Form Labels with icons */
        .form-label i {
            margin-right: 8px;
            color: #8B5E3C;
        }

        /* Inputs */
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid #C9B37F;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #8B5E3C;
            box-shadow: 0 0 10px rgba(139, 94, 60, 0.2);
        }

        /* Invalid / Valid States */
        .is-invalid {
            border-color: #e57373;
            box-shadow: none;
        }

        .is-valid {
            border-color: #6d9c6d;
            box-shadow: none;
        }

        /* Form Text */
        .form-text {
            font-size: 0.85rem;
            color: #BCA18A;
        }

        /* Buttons */
        .btn-primary {
            background-color: #8B5E3C;
            border-color: #8B5E3C;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #4E342E;
            border-color: #4E342E;
            color: #C9B37F;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 1rem 1.25rem;
        }

        /* Link */
        a {
            color: #8B5E3C;
            font-weight: 600;
            text-decoration: none;
        }

        a:hover {
            color: #4E342E;
            text-decoration: underline;
        }

        /* Responsive tweaks */
        @media (max-width: 576px) {
            .card {
                padding: 1.5rem;
            }

            .card h2 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body class="login-body">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </h2>

                        <?php if (isset($success_message)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Usuario
                                </label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($username ?? ''); ?>" required
                                    placeholder="Ingresa tu usuario">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Contraseña
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required
                                    placeholder="Ingresa tu contraseña">
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </button>
                        </form>
                        <p class="mt-3 text-center">
                            ¿No tienes cuenta? <a href="../modelos/register.php" class="fw-bold">Regístrate aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-quitar alertas después de 5 segundos
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function () {
                    alert.remove();
                }, 500);
            });
        }, 5000);

        // Focus en el primer campo al cargar la página
        document.getElementById('username').focus();
    </script>
</body>

</html>