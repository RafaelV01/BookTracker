<?php
include 'includes/auth_check.php';
include 'config/database.php';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$book_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Obtener el libro
$sql = "SELECT * FROM books WHERE id = ? AND user_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id, $user_id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $status = $_POST['status'];

    // Actualizar imagen si se sube una nueva
    $cover_image = $book['cover_image'];
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $cover_image = $target_dir . time() . '_' . basename($_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
        
        // Eliminar la imagen anterior si existe
        if ($book['cover_image'] && file_exists($book['cover_image'])) {
            unlink($book['cover_image']);
        }
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

    $sql = "UPDATE books SET title=?, author=?, genre=?, cover_image=?, total_pages=?, current_page=?, rating=?, review=?, premise=?, status=? 
            WHERE id=? AND user_id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $author, $genre, $cover_image, $total_pages, $current_page, $rating, $review, $premise, $status, $book_id, $user_id]);

    header('Location: dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Libro - BOOKTRACKER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container mt-5">
        <h2>Editar Libro</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $book['title']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="author" class="form-label">Autor</label>
                <input type="text" class="form-control" id="author" name="author" value="<?php echo $book['author']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Género</label>
                <input type="text" class="form-control" id="genre" name="genre" value="<?php echo $book['genre']; ?>">
            </div>
            <div class="mb-3">
                <label for="cover_image" class="form-label">Portada</label>
                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                <?php if ($book['cover_image']): ?>
                    <img src="<?php echo $book['cover_image']; ?>" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="read" <?php echo $book['status'] == 'read' ? 'selected' : ''; ?>>Leído</option>
                    <option value="reading" <?php echo $book['status'] == 'reading' ? 'selected' : ''; ?>>Leyendo</option>
                    <option value="to_read" <?php echo $book['status'] == 'to_read' ? 'selected' : ''; ?>>Por Leer</option>
                </select>
            </div>
            <div id="read-fields" class="status-fields" style="<?php echo $book['status'] == 'read' ? 'display:block;' : 'display:none;'; ?>">
                <div class="mb-3">
                    <label for="rating" class="form-label">Calificación (1-5)</label>
                    <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" value="<?php echo $book['rating']; ?>">
                </div>
                <div class="mb-3">
                    <label for="review" class="form-label">Reseña</label>
                    <textarea class="form-control" id="review" name="review"><?php echo $book['review']; ?></textarea>
                </div>
            </div>
            <div id="reading-fields" class="status-fields" style="<?php echo $book['status'] == 'reading' ? 'display:block;' : 'display:none;'; ?>">
                <div class="mb-3">
                    <label for="total_pages" class="form-label">Total de páginas</label>
                    <input type="number" class="form-control" id="total_pages" name="total_pages" value="<?php echo $book['total_pages']; ?>">
                </div>
                <div class="mb-3">
                    <label for="current_page" class="form-label">Páginas leídas</label>
                    <input type="number" class="form-control" id="current_page" name="current_page" value="<?php echo $book['current_page']; ?>">
                </div>
            </div>
            <div id="to_read-fields" class="status-fields" style="<?php echo $book['status'] == 'to_read' ? 'display:block;' : 'display:none;'; ?>">
                <div class="mb-3">
                    <label for="premise" class="form-label">Premisa</label>
                    <textarea class="form-control" id="premise" name="premise"><?php echo $book['premise']; ?></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script>
        document.getElementById('status').addEventListener('change', function() {
            var status = this.value;
            document.querySelectorAll('.status-fields').forEach(function(field) {
                field.style.display = 'none';
            });
            document.getElementById(status + '-fields').style.display = 'block';
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>