<?php
session_start();
if (!isset($_SESSION['registration_success'])) {
    header('Location: register.php');
    exit;
}

$username = $_SESSION['registered_username'] ?? '';
$message = $_SESSION['registration_success'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Exitoso - BOOKTRACKER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            animation: bounce 1s infinite alternate;
        }
        @keyframes bounce {
            from { transform: scale(1); }
            to { transform: scale(1.1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card success-card">
                    <div class="card-body text-center p-5">
                        <div class="success-icon mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 class="card-title mb-3">¡Registro Exitoso!</h2>
                        <p class="card-text lead mb-4"><?php echo $message; ?></p>
                        
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i>
                            <strong>Usuario registrado:</strong> <?php echo htmlspecialchars($username); ?>
                        </div>
                        
                        <p class="text-muted mb-4">
                            Ahora puedes iniciar sesión y comenzar a gestionar tu biblioteca personal.
                        </p>
                        
                        <a href="login.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Ir a Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Redirección automática después de 5 segundos
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 5000);

        // Contador para redirección
        let countdown = 5;
        const countdownElement = document.createElement('div');
        countdownElement.className = 'text-muted mt-3 small';
        countdownElement.innerHTML = `Redireccionando en <span id="countdown">5</span> segundos...`;
        document.querySelector('.card-body').appendChild(countdownElement);

        setInterval(function() {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            if (countdown <= 0) {
                window.location.href = 'login.php';
            }
        }, 1000);
    </script>
</body>
</html>