<?php
include 'includes/auth_check.php';
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $status = $_POST['status'];

    // Subir imagen de portada
    $cover_image = '';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $cover_image = $target_dir . time() . '_' . basename($_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
    }

    // Campos según el estado
    $total_pages = null;
    $current_page = null;
    $rating = null;
    $review = null;
    $premise = null;

    if ($status == 'reading') {
        $total_pages = $_POST['total_pages'];
        $current_page = $_POST['current_page'];
    } elseif ($status == 'read') {
        $rating = $_POST['rating'];
        $review = $_POST['review'];
    } elseif ($status == 'to_read') {
        $premise = $_POST['premise'];
    }

    $sql = "INSERT INTO books (user_id, title, author, genre, cover_image, total_pages, current_page, rating, review, premise, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $title, $author, $genre, $cover_image, $total_pages, $current_page, $rating, $review, $premise, $status]);

    header('Location: dashboard.php');
}
?>