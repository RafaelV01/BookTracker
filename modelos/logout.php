<?php
session_start();

// Si se confirma el logout via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_logout'])) {
    session_destroy();
    
    // Establecer cookie para mostrar notificación en el index
    setcookie('logout_success', '1', time() + 5, '/'); // Expira en 5 segundos

    header('Location: ../index.php');
    exit;
}

// Si se accede directamente sin confirmación, mostrar página de confirmación
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Cierre de Sesión - BOOKTRACKER</title>
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
        .logout-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card logout-card">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-sign-out-alt fa-3x text-warning mb-3"></i>
                            <h2 class="card-title">Cerrar Sesión</h2>
                        </div>
                        
                        <p class="lead mb-4">¿Estás seguro de que deseas cerrar la sesión?</p>
                        
                        <form method="POST" class="d-flex gap-3 justify-content-center">
                            <button type="submit" name="confirm_logout" value="1" class="btn btn-danger btn-lg">
                                <i class="fas fa-check"></i> Sí, Cerrar Sesión
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>