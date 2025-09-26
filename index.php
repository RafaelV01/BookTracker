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
            background: linear-gradient(135deg, var(--primary-brown, #8B5E3C) 0%, var(--beige, #F5E9DA) 100%);
            color: var(--text-light, #FFF8F0);
            padding: 100px 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Merriweather', serif;
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-brown, #8B5E3C);
            margin-bottom: 1rem;
        }
        .logout-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        .card, .card-body {
            background: var(--beige, #F5E9DA);
            color: var(--text-dark, #3E2723);
        }
        .navbar, .bg-dark, footer.bg-dark {
            background-color: var(--dark-brown, #4E342E) !important;
        }
        .navbar-brand, .navbar-dark .navbar-nav .nav-link, .navbar-dark .navbar-brand {
            color: var(--golden, #C9B37F) !important;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn-primary {
            background-color: var(--primary-brown, #8B5E3C);
            border-color: var(--primary-brown, #8B5E3C);
            color: var(--text-light, #FFF8F0);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--dark-brown, #4E342E);
            border-color: var(--dark-brown, #4E342E);
            color: var(--golden, #C9B37F);
        }
        .btn-outline-light {
            color: var(--primary-brown, #8B5E3C);
            border-color: var(--primary-brown, #8B5E3C);
        }
        .btn-outline-light:hover {
            background-color: var(--primary-brown, #8B5E3C);
            color: var(--text-light, #FFF8F0);
        }
        .text-primary {
            color: var(--primary-brown, #8B5E3C) !important;
        }
        .text-warning {
            color: var(--golden, #C9B37F) !important;
        }
        .text-info {
            color: var(--accent, #BCA18A) !important;
        }
        .text-success {
            color: #6d9c6d !important;
        }
        .alert-success {
            background: linear-gradient(90deg, var(--golden, #C9B37F) 60%, var(--beige, #F5E9DA) 100%);
            color: var(--dark-brown, #4E342E);
            border-color: var(--golden, #C9B37F);
        }
        .alert-danger, .alert-warning {
            background: linear-gradient(90deg, #e57373 60%, var(--beige, #F5E9DA) 100%);
            color: var(--dark-brown, #4E342E);
            border-color: #e57373;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, var(--beige) 0%, var(--brown-700, #A67C52) 100%); color: var(--text-dark, #3E2723); font-family: 'Merriweather', serif;">
    <!-- Navbar para páginas públicas -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background-color: var(--dark-brown, #4E342E);">
        <div class="container">
            <a class="navbar-brand" href="index.php" style="color: var(--golden, #C9B37F);">
                <i class="fas fa-book" style="color: var(--golden, #C9B37F);"></i> BOOKTRACKER
            </a>
            <div class="navbar-nav ms-auto">
                <a href="vistas/login.php" class="btn btn-outline-light me-2" style="color: var(--primary-brown, #8B5E3C); border-color: var(--primary-brown, #8B5E3C);">
                    <i class="fas fa-sign-in-alt" style="color: var(--accent, #BCA18A);"></i> Iniciar Sesión
                </a>
                <a href="modelos/register.php" class="btn btn-primary" style="background-color: var(--primary-brown, #8B5E3C); border-color: var(--primary-brown, #8B5E3C); color: var(--text-light, #FFF8F0);">
                    <i class="fas fa-user-plus" style="color: var(--accent, #BCA18A);"></i> Registrarse
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
            <h1 class="display-4 fw-bold mb-4" style="color: var(--golden, #C9B37F);">
                <i class="fas fa-book-open" style="color: var(--accent, #BCA18A);"></i> BOOKTRACKER
            </h1>
            <p class="lead mb-4" style="color: var(--text-light, #FFF8F0);">Gestiona tu biblioteca personal, lleva el control de tus lecturas y descubre nuevos libros.</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light" style="background: var(--beige, #F5E9DA);">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-books" style="color: var(--primary-brown, #8B5E3C);"></i>
                    </div>
                    <h3 style="color: var(--primary-brown, #8B5E3C);">Gestiona tu Biblioteca</h3>
                    <p class="text-muted" style="color: var(--accent, #BCA18A);">Organiza todos tus libros en una estantería virtual personalizada.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line" style="color: var(--primary-brown, #8B5E3C);"></i>
                    </div>
                    <h3 style="color: var(--primary-brown, #8B5E3C);">Sigue tu Progreso</h3>
                    <p class="text-muted" style="color: var(--accent, #BCA18A);">Lleva el control de tus lecturas y visualiza tu progreso.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-star" style="color: var(--golden, #C9B37F);"></i>
                    </div>
                    <h3 style="color: var(--primary-brown, #8B5E3C);">Califica y Reseña</h3>
                    <p class="text-muted" style="color: var(--accent, #BCA18A);">Guarda tus opiniones y calificaciones de cada libro leído.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4" style="background-color: var(--dark-brown, #4E342E); color: var(--golden, #C9B37F);">
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