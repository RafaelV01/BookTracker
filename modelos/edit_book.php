<?php
include '../vistas/includes/auth_check.php';
include '../config/database.php';

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
    $_SESSION['book_error'] = "Libro no encontrado.";
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $genre = trim($_POST['genre'] ?? '');
    $status = $_POST['status'];

    // Actualizar imagen si se sube una nueva
    $cover_image = $book['cover_image'];
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
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image)) {
                // Eliminar la imagen anterior si existe
                if ($book['cover_image'] && file_exists('../' . $book['cover_image'])) {
                    unlink('../' . $book['cover_image']);
                }
            } else {
                $cover_image = $book['cover_image']; // Mantener la anterior si falla
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
        $sql = "UPDATE books SET title=?, author=?, genre=?, cover_image=?, total_pages=?, current_page=?, rating=?, review=?, premise=?, status=? 
                WHERE id=? AND user_id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $author, $genre, $cover_image, $total_pages, $current_page, $rating, $review, $premise, $status, $book_id, $user_id]);

        $_SESSION['book_success'] = "Libro actualizado exitosamente: " . htmlspecialchars($title);
        header('Location: dashboard.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['book_error'] = "Error al actualizar el libro: " . $e->getMessage();
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Libro - BOOKTRACKER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../vistas/styles/style.css">
</head>
<body>
    <?php include '../vistas/includes/header.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">
                            <i class="fas fa-edit"></i> Editar Libro
                        </h2>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Título *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($book['title']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="author" class="form-label">Autor *</label>
                                        <input type="text" class="form-control" id="author" name="author" 
                                               value="<?php echo htmlspecialchars($book['author']); ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="genre" class="form-label">Género</label>
                                <input type="text" class="form-control" id="genre" name="genre" 
                                       value="<?php echo htmlspecialchars($book['genre']); ?>" 
                                       placeholder="Ej: Novela, Ciencia Ficción, etc.">
                            </div>
                            
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Portada</label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                                <div class="form-text">Formatos aceptados: JPG, PNG, GIF, WebP</div>
                                <?php if ($book['cover_image']): ?>
                                    <div class="mt-2">
                                        <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                             width="100" class="img-thumbnail" alt="Portada actual">
                                        <small class="text-muted d-block">Portada actual</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado *</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="to_read" <?php echo $book['status'] == 'to_read' ? 'selected' : ''; ?>>Por Leer</option>
                                    <option value="reading" <?php echo $book['status'] == 'reading' ? 'selected' : ''; ?>>Leyendo</option>
                                    <option value="read" <?php echo $book['status'] == 'read' ? 'selected' : ''; ?>>Leído</option>
                                </select>
                            </div>
                            
                            <div id="read-fields" class="status-fields" style="<?php echo $book['status'] == 'read' ? 'display:block;' : 'display:none;'; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="rating" class="form-label">Calificación (1-5)</label>
                                            <input type="number" class="form-control" id="rating" name="rating" 
                                                   min="1" max="5" value="<?php echo $book['rating']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="review" class="form-label">Reseña</label>
                                    <textarea class="form-control" id="review" name="review" rows="4" 
                                              placeholder="Tu opinión sobre el libro"><?php echo htmlspecialchars($book['review']); ?></textarea>
                                </div>
                            </div>
                            
                            <div id="reading-fields" class="status-fields" style="<?php echo $book['status'] == 'reading' ? 'display:block;' : 'display:none;'; ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="total_pages" class="form-label">Total de páginas</label>
                                            <input type="number" class="form-control" id="total_pages" name="total_pages" 
                                                   min="1" value="<?php echo $book['total_pages']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="current_page" class="form-label">Páginas leídas</label>
                                            <input type="number" class="form-control" id="current_page" name="current_page" 
                                                   min="0" value="<?php echo $book['current_page']; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="to_read-fields" class="status-fields" style="<?php echo $book['status'] == 'to_read' ? 'display:block;' : 'display:none;'; ?>">
                                <div class="mb-3">
                                    <label for="premise" class="form-label">Premisa o sinopsis</label>
                                    <textarea class="form-control" id="premise" name="premise" rows="4" 
                                              placeholder="Breve descripción o por qué quieres leerlo"><?php echo htmlspecialchars($book['premise']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('status').addEventListener('change', function() {
            var status = this.value;
            document.querySelectorAll('.status-fields').forEach(function(field) {
                field.style.display = 'none';
            });
            document.getElementById(status + '-fields').style.display = 'block';
        });

        // Validación básica del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            var title = document.getElementById('title').value.trim();
            var author = document.getElementById('author').value.trim();
            
            if (!title || !author) {
                e.preventDefault();
                alert('Por favor, completa los campos obligatorios (Título y Autor).');
            }
        });
    </script>
    
    <?php include '../vistas/includes/footer.php'; ?>
</body>
</html>