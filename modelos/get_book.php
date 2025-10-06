<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">ID de libro no proporcionado.</div>';
    exit;
}

$book_id = (int) $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM books WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

if ($book) {
    // Helper de escape
    function h($s)
    {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }

    echo '<div class="row">';
    echo '<div class="col-md-4">';
    $cover_src = $book['cover_image'] ? h($book['cover_image']) : 'https://via.placeholder.com/150x200?text=Sin+Portada';
    echo '<img src="' . $cover_src . '" class="img-fluid rounded mb-3" alt="Portada" style="max-height: 300px; object-fit: cover;">';
    // Botón de descargar PDF si existe
    if (!empty($book['pdf_file'])) {
        $pdf_src = h($book['pdf_file']);
        echo '<div class="d-grid gap-2">';
        echo '<a class="btn btn-outline-primary mb-2" href="' . $pdf_src . '" target="_blank" rel="noopener"><i class="fas fa-external-link"></i> Ver Completo</a>';
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="col-md-8">';
    echo '<h4 class="text-primary">' . h($book['title']) . '</h4>';
    echo '<p><strong><i class="fas fa-user-pen"></i> Autor:</strong> ' . h($book['author']) . '</p>';
    echo '<p><strong><i class="fas fa-bookmark"></i> Género:</strong> ' . ($book['genre'] ? h($book['genre']) : 'No especificado') . '</p>';

    // Mostrar información según el estado
    switch ($book['status']) {
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
                echo '<div class="bg-light p-3 rounded">' . nl2br(h($book['review'])) . '</div>';
            }
            break;

        case 'reading':
            echo '<p><strong><i class="fas fa-book-reader text-warning"></i> Estado:</strong> Leyendo</p>';
            if ($book['total_pages'] > 0) {
                $percentage = min(100, ($book['current_page'] / $book['total_pages']) * 100);
                echo '<p><strong>Progreso:</strong> ' . (int) $book['current_page'] . ' / ' . (int) $book['total_pages'] . ' páginas</p>';
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
                echo '<div class="bg-light p-3 rounded">' . nl2br(h($book['premise'])) . '</div>';
            }
            break;
    }

    echo '<div class="mt-3">';
    echo '<a href="edit_book.php?id=' . $book['id'] . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Editar</a> ';
    echo '<a href="delete_book.php?id=' . $book['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Seguro?\')"><i class="fas fa-trash"></i> Eliminar</a>';
    echo '</div>';

    echo '</div>'; // .col-md-8
    echo '</div>'; // .row

    // Mostrar iframe con PDF si existe (ocupa toda la anchura)
    if (!empty($book['pdf_file'])) {
        $pdf_src = h($book['pdf_file']);
        echo '<hr class="my-3">';
        echo '<div style="height:520px;">';
        echo '<iframe src="' . $pdf_src . '" frameborder="0" style="width:100%; height:100%;" allowfullscreen></iframe>';
        echo '</div>';
    }

} else {
    echo '<div class="alert alert-warning">';
    echo '<i class="fas fa-exclamation-triangle"></i> Libro no encontrado.';
    echo '</div>';
}
