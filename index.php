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
        body {
            background: linear-gradient(135deg, var(--beige, #F5E9DA) 0%, var(--brown-700, #A67C52) 100%);
            color: var(--text-dark, #3E2723);
            font-family: 'Merriweather', serif;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-brown, #8B5E3C) 0%, var(--beige, #F5E9DA) 100%);
            color: var(--text-light, #FFF8F0);
            padding: 120px 0;
            min-height: 50vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.2);
        }

        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-section p {
            font-size: 1.25rem;
            max-width: 700px;
            margin: auto;
            line-height: 1.6;
        }

        /* Navbar */
        .navbar {
            background-color: var(--dark-brown, #4E342E) !important;
            padding: 0.8rem 1rem;
        }

        .navbar-brand,
        .navbar-dark .navbar-nav .nav-link {
            color: var(--golden, #C9B37F) !important;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background-color: var(--primary-brown, #8B5E3C);
            border-color: var(--primary-brown, #8B5E3C);
            color: var(--text-light, #FFF8F0);
            transition: all 0.3s ease;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--dark-brown, #4E342E);
            border-color: var(--dark-brown, #4E342E);
            color: var(--golden, #C9B37F);
        }

        .btn-outline-light {
            color: var(--primary-brown, #8B5E3C);
            border-color: var(--primary-brown, #8B5E3C);
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background-color: var(--primary-brown, #8B5E3C);
            color: var(--text-light, #FFF8F0);
        }

        /* Feature Icons */
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-brown, #8B5E3C);
            margin-bottom: 1rem;
        }

        .feature-icon i {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .feature-icon i:hover {
            transform: scale(1.2);
            color: var(--golden, #C9B37F);
        }

        /* Feature Cards */
        .features-section h3 {
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .features-section p {
            color: var(--accent, #BCA18A);
            font-size: 0.95rem;
        }

        /* Footer */
        footer {
            background-color: var(--dark-brown, #4E342E);
            color: var(--golden, #C9B37F);
            padding: 10px;
            font-size: 0.9rem;
        }

        /* Alerts */
        .logout-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .alert-success {
            background: linear-gradient(90deg, var(--golden, #C9B37F) 60%, var(--beige, #F5E9DA) 100%);
            color: var(--dark-brown, #4E342E);
            border-color: var(--golden, #C9B37F);
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                BOOKTRACKER
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

    <!-- Logout Notification -->
    <?php if (isset($logout_message)): ?>
        <div class="alert alert-success alert-dismissible fade show logout-alert" role="alert">
            <strong><i class="fas fa-check-circle"></i> ¡Éxito!</strong> <?php echo $logout_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-book-open"></i> BOOKTRACKER</h1>
            <p>Gestiona tu biblioteca personal, lleva el control de tus lecturas y descubre nuevos libros.</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5" style="background: var(--beige, #F5E9DA);">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon"><i class="fas  fa-book"></i></div>
                    <h3>Gestiona tu Biblioteca</h3>
                    <p>Organiza todos tus libros en una estantería virtual personalizada.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Sigue tu Progreso</h3>
                    <p>Lleva el control de tus lecturas y visualiza tu progreso.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon"><i class="fas fa-star"></i></div>
                    <h3>Califica y Reseña</h3>
                    <p>Guarda tus opiniones y calificaciones de cada libro leído.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p>&copy; 2024 BOOKTRACKER. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alert después de 5 segundos
        setTimeout(function () {
            var alert = document.querySelector('.alert');
            if (alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    </script>
</body>

</html>