<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verificar que el libro pertenece al usuario
$sql = "SELECT * FROM books WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

if ($book) {
    // Eliminar la imagen si existe
    if ($book['cover_image'] && file_exists('../' . $book['cover_image'])) {
        unlink('../' . $book['cover_image']);
    }

    try {
        $sql = "DELETE FROM books WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$book_id, $user_id]);

        $_SESSION['book_success'] = "Libro eliminado exitosamente: " . htmlspecialchars($book['title']);
    } catch (PDOException $e) {
        $_SESSION['book_error'] = "Error al eliminar el libro: " . $e->getMessage();
    }
} else {
    $_SESSION['book_error'] = "Libro no encontrado o no tienes permisos para eliminarlo.";
}

header('Location: dashboard.php');
exit;
?>