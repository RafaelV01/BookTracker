<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre'] ?? '');
    $status = $_POST['status'];

    // Subir imagen de portada
    $cover_image = '';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Validar tipo de archivo
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = $_FILES['cover_image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $cover_image = $target_dir . time() . '_' . basename($_FILES['cover_image']['name']);
            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image)) {
                $cover_image = ''; // Si falla la subida, dejar vacío
            }
        }
    }

    // Campos según el estado
    $total_pages = null;
    $current_page = null;
    $rating = null;
    $review = null;
    $premise = null;

    if ($status == 'reading') {
        $total_pages = !empty($_POST['total_pages']) ? (int)$_POST['total_pages'] : null;
        $current_page = !empty($_POST['current_page']) ? (int)$_POST['current_page'] : null;
    } elseif ($status == 'read') {
        $rating = !empty($_POST['rating']) ? (int)$_POST['rating'] : null;
        $review = trim($_POST['review'] ?? '');
    } elseif ($status == 'to_read') {
        $premise = trim($_POST['premise'] ?? '');
    }

    try {
        $sql = "INSERT INTO books (user_id, title, author, genre, cover_image, total_pages, current_page, rating, review, premise, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $user_id, 
            $title, 
            $author, 
            $genre, 
            $cover_image, 
            $total_pages, 
            $current_page, 
            $rating, 
            $review, 
            $premise, 
            $status
        ]);

        $_SESSION['book_success'] = "Libro agregado exitosamente: " . htmlspecialchars($title);
        header('Location: dashboard.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['book_error'] = "Error al agregar el libro: " . $e->getMessage();
        header('Location: dashboard.php');
        exit;
    }
} else {
    // Si no es POST, redirigir al dashboard
    header('Location: dashboard.php');
    exit;
}
?>