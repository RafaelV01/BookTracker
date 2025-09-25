<?php
include 'includes/auth_check.php';
include 'config/database.php';

if (!isset($_GET['id'])) {
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM books WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

if ($book) {
    echo '<img src="' . ($book['cover_image'] ? $book['cover_image'] : 'https://via.placeholder.com/150x200?text=Sin+Portada') . '" class="img-fluid mb-3" alt="Portada">';
    echo '<h5>' . $book['title'] . '</h5>';
    echo '<p><strong>Autor:</strong> ' . $book['author'] . '</p>';
    echo '<p><strong>Género:</strong> ' . $book['genre'] . '</p>';
    if ($book['status'] == 'read') {
        echo '<p><strong>Calificación:</strong> ' . $book['rating'] . '/5</p>';
        echo '<p><strong>Reseña:</strong> ' . nl2br($book['review']) . '</p>';
    }
} else {
    echo 'Libro no encontrado.';
}
?>