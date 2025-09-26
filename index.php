<?php
session_start();

// Mostrar notificación de logout exitoso
if (isset($_COOKIE['logout_success'])) {
    $logout_message = "Sesión cerrada correctamente. ¡Vuelve pronto!";
    setcookie('logout_success', '', time() - 3600, '/'); // Eliminar cookie
}

// Si el usuario está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: modelos/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOKTRACKER - Tu gestor de libros personal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="vistas/styles/style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .feature-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        .logout-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
    </style>
</head>
<body>
    <!-- Navbar para páginas públicas -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-book"></i> BOOKTRACKER
            </a>
            <div class="navbar-nav ms-auto">
                <a href="vistas/login.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                <a href="modelos/register.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Registrarse
                </a>
            </div>
        </div>
    </nav>

    <!-- Notificación de logout -->
    <?php if (isset($logout_message)): ?>
    <div class="alert alert-success alert-dismissible fade show logout-alert" role="alert">
        <strong><i class="fas fa-check-circle"></i> ¡Éxito!</strong> <?php echo $logout_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-book-open"></i> BOOKTRACKER
            </h1>
            <p class="lead mb-4">Gestiona tu biblioteca personal, lleva el control de tus lecturas y descubre nuevos libros.</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-books"></i>
                    </div>
                    <h3>Gestiona tu Biblioteca</h3>
                    <p class="text-muted">Organiza todos tus libros en una estantería virtual personalizada.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Sigue tu Progreso</h3>
                    <p class="text-muted">Lleva el control de tus lecturas y visualiza tu progreso.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Califica y Reseña</h3>
                    <p class="text-muted">Guarda tus opiniones y calificaciones de cada libro leído.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; 2024 BOOKTRACKER. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alert después de 5 segundos
        setTimeout(function() {
            var alert = document.querySelector('.alert');
            if (alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    </script>
</body>
</html>