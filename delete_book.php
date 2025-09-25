<?php
include 'includes/auth_check.php';
include 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Verificar que el libro pertenece al usuario y no es leído (solo se pueden eliminar en proceso y por leer)
$sql = "SELECT * FROM books WHERE id = ? AND user_id = ? AND status IN ('reading', 'to_read')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

if ($book) {
    // Eliminar la imagen si existe
    if ($book['cover_image'] && file_exists($book['cover_image'])) {
        unlink($book['cover_image']);
    }
    
    $sql = "DELETE FROM books WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$book_id]);
}

header('Location: dashboard.php');
?>