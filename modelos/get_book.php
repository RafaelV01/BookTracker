<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">ID de libro no proporcionado.</div>';
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM books WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

if ($book) {
    echo '<div class="row">';
    echo '<div class="col-md-4">';
    echo '<img src="' . ($book['cover_image'] ? htmlspecialchars($book['cover_image']) : 'https://via.placeholder.com/150x200?text=Sin+Portada') . '" class="img-fluid rounded mb-3" alt="Portada" style="max-height: 300px; object-fit: cover;">';
    echo '</div>';
    echo '<div class="col-md-8">';
    echo '<h4 class="text-primary">' . htmlspecialchars($book['title']) . '</h4>';
    echo '<p><strong><i class="fas fa-user-pen"></i> Autor:</strong> ' . htmlspecialchars($book['author']) . '</p>';
    echo '<p><strong><i class="fas fa-bookmark"></i> Género:</strong> ' . ($book['genre'] ? htmlspecialchars($book['genre']) : 'No especificado') . '</p>';
    
    // Mostrar información según el estado
    switch($book['status']) {
        case 'read':
            echo '<p><strong><i class="fas fa-check-circle text-success"></i> Estado:</strong> Leído</p>';
            if ($book['rating']) {
                echo '<p><strong><i class="fas fa-star text-warning"></i> Calificación:</strong> ';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<i class="fas fa-star' . ($i <= $book['rating'] ? ' text-warning' : ' text-muted') . '"></i>';
                }
                echo ' (' . $book['rating'] . '/5)</p>';
            }
            if ($book['review']) {
                echo '<p><strong><i class="fas fa-comment"></i> Reseña:</strong></p>';
                echo '<div class="bg-light p-3 rounded">' . nl2br(htmlspecialchars($book['review'])) . '</div>';
            }
            break;
            
        case 'reading':
            echo '<p><strong><i class="fas fa-book-reader text-warning"></i> Estado:</strong> Leyendo</p>';
            if ($book['total_pages'] > 0) {
                $percentage = min(100, ($book['current_page'] / $book['total_pages']) * 100);
                echo '<p><strong>Progreso:</strong> ' . $book['current_page'] . ' / ' . $book['total_pages'] . ' páginas</p>';
                echo '<div class="progress mb-2" style="height: 10px;">';
                echo '<div class="progress-bar bg-warning" role="progressbar" style="width: ' . $percentage . '%;"></div>';
                echo '</div>';
                echo '<small class="text-muted">' . round($percentage, 1) . '% completado</small>';
            }
            break;
            
        case 'to_read':
            echo '<p><strong><i class="fas fa-bookmark text-info"></i> Estado:</strong> Por Leer</p>';
            if ($book['premise']) {
                echo '<p><strong><i class="fas fa-file-lines"></i> Premisa:</strong></p>';
                echo '<div class="bg-light p-3 rounded">' . nl2br(htmlspecialchars($book['premise'])) . '</div>';
            }
            break;
    }
    
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="alert alert-warning">';
    echo '<i class="fas fa-exclamation-triangle"></i> Libro no encontrado.';
    echo '</div>';
}
?>